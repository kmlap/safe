require('dotenv').config();

const moment = require('moment');
const WebSocket = require('ws');
const pako = require('pako');
const SocksProxyAgent = require('socks-proxy-agent');
const HttpsProxyAgent = require('https-proxy-agent');
const redis = require('./ioredis');
const CronJob = require('cron').CronJob;

const WS_URL = 'wss://api.hadax.com/ws';
const symbols = require('../config/symbols');
const periods = require('../config/periods');

let ws;
let connecting = false;
let timestamp = 0;
let timer = [];
let ticker = {
    btcusdt: {open: null, high: null, low: null},
    ethusdt: {open: null, high: null, low: null},
    eosusdt: {open: null, high: null, low: null},
    ltcusdt: {open: null, high: null, low: null},
    htusdt: {open: null, high: null, low: null},
    dogeusdt: {open: null, high: null, low: null},
    dotusdt: {open: null, high: null, low: null},
    xrpusdt: {open: null, high: null, low: null},
    linkusdt: {open: null, high: null, low: null},
    bchusdt: {open: null, high: null, low: null},
    filusdt: {open: null, high: null, low: null},
};

symbols.forEach(async symbol => {
    const open = await redis.get(`ticker:${symbol}:open`);
    const high = await redis.get(`ticker:${symbol}:high`);
    const low = await redis.get(`ticker:${symbol}:low`);
    if (open !== null) {
        ticker[symbol].open = open;
    }
    if (high !== null) {
        ticker[symbol].high = high;
    }
    if (low !== null) {
        ticker[symbol].low = low;
    }
});

// 创建连接
function connect() {
    if (process.env.APP_ENV === 'production') {
        ws = new WebSocket(WS_URL);
    } else {
        let agent;
        if (process.env.PROXY_TYPE === 'http') {
            agent = new HttpsProxyAgent(process.env.PROXY);
        } else {
            agent = new SocksProxyAgent(process.env.PROXY);
        }
        ws = new WebSocket(WS_URL, {agent: agent});
    }
    registerEventListener();
}

// 重新连接
function reconnect() {
    timer.forEach(item => clearTimeout(item));
    if (connecting) return;
    connecting = true;
    setTimeout(() => {
        connect();
        connecting = false;
    }, 1000);
}

function registerEventListener() {
    ws.on('open', () => {
        console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] open`);
        subscribe(ws);
    });

    ws.on('message', data => {
        let text = pako.inflate(data, {to: 'string'});
        let msg = JSON.parse(text);
        if (msg.ping) {
            timestamp = msg.ping;
            ws.send(JSON.stringify({pong: timestamp}));
        } else if (msg.tick) {
            subscribeHandle(msg);
        } else if (msg.rep) {
            requestHandle(msg);
        } else {
            console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] ${text}`);
        }
    });

    ws.on('close', () => {
        console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] close`);
        reconnect();
    });

    ws.on('error', error => {
        console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] ${error}`);
        reconnect();
    });
}

// 订阅
function subscribe(ws) {
    for (let symbol of symbols) {
        // 订阅深度
        // 谨慎选择合并的深度，ws每次推送全量的深度数据，若未能及时处理容易引起消息堆积并且引发行情延时
        ws.send(JSON.stringify({
            "sub": `market.${symbol}.depth.step0`,
            "id": `${symbol}`
        }));

        // 订阅K线
        for (let i = 0; i < periods.length; i++) {
            const period = periods[i];
            ws.send(JSON.stringify({
                "sub": `market.${symbol}.kline.${period}`,
                "id": `${symbol}`
            }));
        }

        // 此主题提供市场最新成交明细。
        ws.send(JSON.stringify({
            "sub": `market.${symbol}.trade.detail`,
            "id": `${symbol}`
        }));

        // 此主题提供24小时内最新市场概要。
        ws.send(JSON.stringify({
            "sub": `market.${symbol}.detail`,
            "id": `${symbol}`
        }));
    }
}

// 订阅消息处理器
function subscribeHandle(data) {
    const symbol = data.ch.split('.')[1];
    const channel = data.ch.split('.')[2];
    switch (channel) {
        case 'depth':
            console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] depth:${symbol}`);
            depthHandle(symbol, data);
            break;
        case 'kline':
            const period = data.ch.split('.')[3];
            console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] kline:${symbol}:${period}`);
            klineHandle(symbol, period, data);
            break;
        case 'trade':
            console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] trade:${symbol}`);
            tradeHandle(symbol, data);
            break;
        case 'detail':
            console.log(`[${moment().format('YYYY-MM-DD HH:mm:ss')}] ticker:${symbol}`);
            tickerHandle(symbol, data);
            break;
    }
}

// 请求消息处理器
function requestHandle(data) {
    const symbol = data.rep.split('.')[1];
    const channel = data.rep.split('.')[2];
    switch (channel) {
        case 'kline':
            const period = data.rep.split('.')[3];
            klineHandle(symbol, period, data);
            break;
        default:
            break;
    }
}

// 深度数据处理器
async function depthHandle(symbol, data) {
    if (symbol === 'htusdt') {
        let priceOffset = 0;
        priceOffset = await redis.get('price_offset:htusdt');
        priceOffset = Number(priceOffset);

        const asks = data.tick.asks.slice(0, 100);
        asks.map(item => item.reverse());
        asks.map(item => item[1] += priceOffset);
        asks.unshift(`huobi:depth:${symbol}:asks`);
        redis.del(`huobi:depth:${symbol}:asks`);
        redis.zadd.apply(redis, asks);

        const bids = data.tick.bids.slice(0, 100);
        bids.map(item => item.reverse());
        bids.map(item => item[1] += priceOffset);
        bids.unshift(`huobi:depth:${symbol}:bids`);
        redis.del(`huobi:depth:${symbol}:bids`);
        redis.zadd.apply(redis, bids);
    } else {
        const asks = data.tick.asks.slice(0, 100);
        asks.map(item => item.reverse());
        asks.unshift(`huobi:depth:${symbol}:asks`);
        redis.del(`huobi:depth:${symbol}:asks`);
        redis.zadd.apply(redis, asks);

        const bids = data.tick.bids.slice(0, 100);
        bids.map(item => item.reverse());
        bids.unshift(`huobi:depth:${symbol}:bids`);
        redis.del(`huobi:depth:${symbol}:bids`);
        redis.zadd.apply(redis, bids);
    }
}

// 交易数据处理器
async function tradeHandle(symbol, data) {
    let priceOffset = 0;
    if (symbol === 'htusdt') {
        priceOffset = await redis.get('price_offset:htusdt');
        priceOffset = Number(priceOffset);
    }
    for (let trade of data.tick.data) {
        if (symbol === 'htusdt') {
            trade.price += priceOffset;
        }
        redis.lpush(`huobi:trade:${symbol}`, JSON.stringify({
            id: data.tick.id,
            side: trade.direction.toUpperCase(),
            price: trade.price.toFixed(8),
            number: trade.amount.toFixed(8),
            created_at: moment(data.tick.ts).utcOffset(8).format('YYYY-MM-DD HH:mm:ss'),
        }));
    }
    redis.ltrim(`huobi:trade:${symbol}`, 0, 99);
}

// 行情数据处理器
async function tickerHandle(symbol, data) {
    if (symbol === 'htusdt') {
        let priceOffset = await redis.get('price_offset:htusdt');
        data.tick.close += Number(priceOffset);
    }

    if (ticker[symbol].open === null) {
        ticker[symbol].open = data.tick.close;
        await redis.set(`ticker:${symbol}:open`, ticker[symbol].open);
    }
    if (ticker[symbol].high === null || data.tick.close > ticker[symbol].high) {
        ticker[symbol].high = data.tick.close;
        await redis.set(`ticker:${symbol}:high`, ticker[symbol].high);
    }
    if (ticker[symbol].low === null || data.tick.close < ticker[symbol].low) {
        ticker[symbol].low = data.tick.close;
        await redis.set(`ticker:${symbol}:low`, ticker[symbol].low);
    }

    await redis.set(`huobi:ticker:${symbol}`, JSON.stringify({
        open: Number(ticker[symbol].open).toFixed(8),
        close: data.tick.close.toFixed(8),
        high: Number(ticker[symbol].high).toFixed(8),
        low: Number(ticker[symbol].low).toFixed(8),
        number: data.tick.amount.toFixed(8),
        total: data.tick.vol.toFixed(8),
    }));

    await redis.sadd(`huobi:ticker_prices:${symbol}`, data.tick.close.toFixed(8));
}

// K线数据处理器
async function klineHandle(symbol, period, data) {
    if (symbol === 'htusdt') {
        let priceOffset = await redis.get('price_offset:htusdt');
        priceOffset = priceOffset ? Number(priceOffset) : 0;
        let result = await redis.zrangebyscore(`huobi:kline:${symbol}:${period}`, data.tick.id, data.tick.id);
        if (result.length === 0) {
            data.tick.open += priceOffset;
            data.tick.close += priceOffset;
            data.tick.high += priceOffset;
            data.tick.low += priceOffset;
        } else {
            result = JSON.parse(result);
            data.tick.open = Number(result[1]);
            data.tick.close += priceOffset;
            if (data.tick.close > result[2]) {
                data.tick.high = data.tick.close;
            } else {
                data.tick.high = Number(result[2]);
            }
            if (data.tick.close < result[3]) {
                data.tick.low = data.tick.close;
            } else {
                data.tick.low = Number(result[3]);
            }
        }
    }
    redis.kline(
        `huobi:kline:${symbol}:${period}`,
        data.tick.id,
        JSON.stringify([
            data.tick.id,
            data.tick.open.toFixed(8),
            data.tick.high.toFixed(8),
            data.tick.low.toFixed(8),
            data.tick.close.toFixed(8),
            data.tick.amount.toFixed(8),
            data.tick.vol.toFixed(8),
            data.tick.count,
        ])
    );
}

connect();

// 定时检查连接状态
setInterval(() => {
    // 连接已中断，立即重新连接
    if (ws.readyState === WebSocket.CLOSED) {
        reconnect();
        return;
    }
    // 连接未中断，但超过 10 秒未收到消息，关闭连接
    if (Date.now() - timestamp > 10000) {
        ws.close();
    }
}, 10 * 1000); // 每10秒执行

// K线数据裁剪
setInterval(() => {
    symbols.forEach(symbol => {
        periods.forEach(period => {
            redis.zremrangebyrank(`huobi:kline:${symbol}:${period}`, 0, -1001);
        });
    });
}, 60 * 60 * 1000); // 每小时执行

// 每天0点重置ticker统计
new CronJob('0 0 0 * * *', function () {
    symbols.forEach(async symbol => {
        let result = await redis.get(`huobi:ticker:${symbol}`);
        result = JSON.parse(result);
        ticker[symbol].open = result.close;
        ticker[symbol].high = result.close;
        ticker[symbol].low = result.close;
        await redis.set(`ticker:${symbol}:open`, ticker[symbol].open);
        await redis.set(`ticker:${symbol}:high`, ticker[symbol].high);
        await redis.set(`ticker:${symbol}:low`, ticker[symbol].low);
    });
}, null, false, 'Asia/Tokyo').start();
