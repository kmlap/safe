const uuid = require('uuid');
const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 1414 });

const compress = require('./compress');

const FIVE_SECOND = 5 * 1000;
const TEN_SECOND = 10 * 1000;

wss.sockets = new Map();
wss.channels = new Map();

wss.on('connection', (client) => {
    // 创建连接时为客户端分配唯一 ID
    client.id = uuid.v4();
    client.time = Date.now();

    wss.sockets.set(client.id, client);

    client.on('message', (message) => {
        try {
            message = JSON.parse(message);
        } catch (e) {
            client.send(compress({
                error: "请发送json格式的消息",
            }));
            return;
        }

        //检查客户端发送过来的消息格式
        //json格式
        if (typeof message !== "object") {
            client.send(compress({
                error: "请发送json格式的消息",
            }));
            return;
        }

        if (message.pong || message.sub || message.unsub || message.req) {
            // 处理客户端心跳消息
            if (message.pong) {
                client.time = Date.now();
                return;
            }
            // 处理客户端订阅消息
            if (message.sub) {
                join(client, message.sub);
                client.send(compress({
                    status: true,
                    subbed: message.sub,
                    ts: Date.now()
                }));
                return;
            }

            // 处理客户端退订消息
            if (message.unsub) {
                leave(client, message.unsub);
                client.send(compress({
                    status: true,
                    unsubbed: message.unsub,
                    ts: Date.now()
                }));
                return;
            }

            // TODO 处理客户端请求消息
            if (message.req) {
                return;
            }
        } else {
            client.send(compress({
                error: "方法不存在",
            }));
            return;
        }
    });

    client.on('close', async () => {
        // 当连接断开时清除客户端所有订阅频道
        wss.sockets.delete(client.id);
        if (client.channels !== undefined) {
            client.channels.forEach((channel) => {
                leave(client, channel);
            });
        }
    });
});

// 向指定频道发送消息
wss.to = async (channel, message) => {
    const clients = wss.channels.get(channel);
    if (clients !== undefined) {
        clients.forEach((clientId) => {
            wss.sockets.get(clientId).send(compress(message));
        });
    }
}

function join(client, channel) {
    let clients = wss.channels.get(channel);
    if (clients === undefined) {
        clients = new Set();
    }
    clients.add(client.id);
    wss.channels.set(channel, clients);

    if (client.channels === undefined) {
        client.channels = new Set();
    }
    client.channels.add(channel);
}

function leave(client, channel) {
    let clients = wss.channels.get(channel);
    if (clients !== undefined) {
        clients.delete(client.id);
        if (clients.size === 0) {
            wss.channels.delete(channel);
        } else {
            wss.channels.set(channel, clients);
        }
    }

    if (client.channels !== undefined) {
        client.channels.delete(channel);
    }
}

/**
 * 每 5 秒向所有客户端发送时间戳
 */
setInterval(() => {
    const message = {
        ping: Date.now()
    };
    wss.clients.forEach((client) => {
        client.send(compress(message));
    });
}, FIVE_SECOND);

/**
 * 每 10 秒检查客户端最后响应时间戳
 * 若与当前时间戳相隔大于 10 秒
 * 则断开连接
 */
if (process.env.APP_ENV === 'production') {
    setInterval(() => {
        wss.clients.forEach((client) => {
            if (Date.now() - client.time > TEN_SECOND) {
                client.terminate();
            }
        });
    }, TEN_SECOND);
}

module.exports = wss;
