const env = require('dotenv').config().parsed;
const mqtt = require('async-mqtt');
const koa = require('koa');
const bodyparser = require('koa-bodyparser');
const validator = require('validator');
const client = mqtt.connect(env.uri);
const app = new koa();

console.log();

app.use(bodyparser());
app.use(async (ctx) => {
    const param = ctx.request.body;
    if (ctx.method !== 'POST' ||
        typeof param !== "object" ||
        !param.hasOwnProperty('topic') ||
        !param.hasOwnProperty('message') ||
        !param.hasOwnProperty('options')) {
        ctx.body = {
            error: 1,
            msg: 'Request method or parameter is not standardized.'
        };
        return;
    }

    try {
        await client.publish(param.topic, param.message, param.options);
        ctx.body = {
            error: 0,
            msg: 'Successfully released.'
        };
    } catch (e) {
        ctx.body = {
            error: 1,
            msg: 'The message was posted abnormally.'
        };
    }
});

client.on('connect', () => {
    app.listen(3000);
});
