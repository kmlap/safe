module.exports = {
    apps: [{
        name: 'toffs-huobi-ws',
        script: './src/index.js',

        // Options reference: https://pm2.io/doc/en/runtime/reference/ecosystem-file/
        args: '',
        instances: 1,
        autorestart: true,
        watch: false,
        max_memory_restart: '1G',
        kill_timeout: 10 * 1000,
        env: {
            NODE_ENV: 'development'
        },
        env_production: {
            NODE_ENV: 'production'
        }
    }],
};
