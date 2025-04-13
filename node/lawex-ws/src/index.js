require('dotenv').config();

const wss = require('./websocket');
const Redis = require('./ioredis');
const redis = new Redis();
const sub = new Redis();
const symbols = require('../config/symbols');
const periods = require('../config/periods');
const levels = require('../config/depth_levels');

setInterval(() => {
    Object.keys(symbols).forEach(function (symbol) {
    	//console.log(symbol);
	huobiDepthHandle(symbol);
    });
}, 100);

sub.psubscribe('__keyspace@12__:*', (error) => {
    if (error) {
        console.error('error:', error);
    } else {
        console.log('订阅:* 成功');

    }
});

sub.on('pmessage', (channel, event, op) => {
     //console.log('cding',event);
    const keys = event.split(':');
    const node = keys[1];
    const type = keys[2];

    if (node === 'huobi') {
        switch (type) {
            case 'kline':
                if ('zrem' !== op) {
                    huobiKlineHandle(keys);
                }
                break;
            case 'ticker':
                huobiTickerHandle(keys);
                break;
            case 'trade':
                if ('ltrim' !== op) {
                    huobiTradeHandle(keys)
                }
                break;
            default:
                break;
        }
    }

});

let huobiDepthMsg = {};

async function huobiDepthHandle(symbol) {
    const asks = await redis.zrange(`huobi:depth:${symbol}:asks`, 0, 99, 'WITHSCORES')
        .then(result => {
            let data = {};
            for (let i = 0; i < result.length; i += 2) {
                data[result[i]] = result[i + 1];
            }
            const asks = [];
            Object.keys(data).sort().forEach(function (key) {
                asks.push([Number(key).toFixed(8), Number(data[key]).toFixed(8)]);
            });
            return asks;
        });

    const bids = await redis.zrange(`huobi:depth:${symbol}:bids`, 0, 99, 'WITHSCORES')
        .then(result => {
            let data = {};
            for (let i = 0; i < result.length; i += 2) {
                data[result[i]] = result[i + 1];
            }
            const bids = [];
            Object.keys(data).sort().reverse().forEach(function (key) {
                bids.push([Number(key).toFixed(8), Number(data[key]).toFixed(8)]);
            });
            return bids;
        });

    const channel = `depth:${symbols[symbol]}`;
    const message = JSON.stringify({
        channel: channel,
        data: {
            asks: asks,
            bids: bids
        }
    });
    //console.log(message);
    if (huobiDepthMsg[channel] !== message) {
        huobiDepthMsg[channel] = message;
        wss.to(channel, message);
    }

    levels.forEach(function (level) {
        const channel = `depth:${symbols[symbol]}:${level}`;
        const message = JSON.stringify({
            channel: channel,
            data: {
                asks: asks.slice(0, level),
                bids: bids.slice(0, level)
            }
        });

        if (huobiDepthMsg[channel] !== message) {
            huobiDepthMsg[channel] = message;
            wss.to(channel, message)
        }
    })
}

async function huobiKlineHandle(keys) {
    const node = keys[1];
    const symbol = keys[3];
    const period = keys[4];

    const klines = await redis.zrange(`${node}:kline:${symbol}:${period}`, -1, -1)
    let data = [];
    if (klines.length > 0) {
        let kline = JSON.parse(klines[0]);
        data = [
            kline[0],
            Number(kline[1]).toFixed(8),
            Number(kline[2]).toFixed(8),
            Number(kline[3]).toFixed(8),
            Number(kline[4]).toFixed(8),
            Number(kline[5]).toFixed(8),
            Number(kline[6]).toFixed(8),
            kline[7],
        ];
    }
    const channel = `kline:${symbols[symbol]}:${periods[period]}`;
    const message = JSON.stringify({
        channel: channel,
        data: data
    });
    wss.to(channel, message);
}

let huobiTickerMsg = {};

async function huobiTickerHandle(keys) {
    const node = keys[1];
    const symbol = keys[3];
    let ticker = JSON.parse(await redis.get(`${node}:ticker:${symbol}`));
    ticker['number'] = Number(ticker['number']).toFixed(8);
    ticker['total'] = Number(ticker['total']).toFixed(8);
    ticker['symbol'] = symbols[symbol];

    let channel = `ticker:${symbols[symbol]}`;
    let message = JSON.stringify({
        channel: channel,
        data: ticker
    });
    if (huobiTickerMsg[channel] !== message) {
        huobiTickerMsg[channel] = message;
        wss.to(channel, message);
    }

    channel = `ticker`;
    message = JSON.stringify({
        channel: channel,
        data: ticker
    });
    if (huobiTickerMsg[channel] !== message) {
        huobiTickerMsg[channel] = message;
        wss.to(channel, message);
    }
}

let huobiTradeMsg = {};

async function huobiTradeHandle(keys) {
    const node = keys[1];
    const symbol = keys[3];
    const list = await redis.lrange(`${node}:trade:${symbol}`, 0, -1);
    let data = list.map(function (item) {
        item = JSON.parse(item);
        item.price = Number(item.price).toFixed(8);
        item.number = Number(item.number).toFixed(8);
        return item;
    });
    const channel = `trade:${symbols[symbol]}`;
    const message = JSON.stringify({
        channel: channel,
        data: data
    });
    if (huobiTradeMsg[channel] !== message) {
        huobiTradeMsg[channel] = message;
        wss.to(channel, message);
    }
}

