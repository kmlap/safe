const IORedis = require('ioredis');

class Redis {
  constructor() {
    const redis = new IORedis({
      host: process.env.REDIS_HOST,
      port: process.env.REDIS_PORT,
      password: process.env.REDIS_PASSWORD,
        db: process.env.REDIS_DB
    });

    redis.on("error", error => console.log("Redis error: " + error));
    return redis;
  }
}

module.exports = Redis;
