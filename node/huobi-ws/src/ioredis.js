const Redis = require('ioredis');

const redis = new Redis({
  host: process.env.REDIS_HOST,
  port: process.env.REDIS_PORT,
  password: process.env.REDIS_PASSWORD,
  db: process.env.REDIS_DB
});

redis.on("error", error => console.log("Redis error: " + error));

redis.defineCommand('kline', {
  numberOfKeys: 1,
  lua: `
  local result = redis.call('zrangebyscore', KEYS[1], ARGV[1], ARGV[1])
  if result ~= '' then
    for i = 1, #result do
      redis.call('zrem', KEYS[1], result[i])
    end
    return redis.call('zadd', KEYS[1], ARGV[1], ARGV[2])
  else
    return redis.call('zadd', KEYS[1], ARGV[1], ARGV[2])
  end
  `
});

module.exports = redis;
