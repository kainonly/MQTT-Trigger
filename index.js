const env = require('dotenv').config().parsed;
const mqtt = require('async-mqtt');
const joi = require('joi');
const koa = require('koa');
const bodyparser = require('koa-bodyparser');
const client = mqtt.connect(env.uri);
const app = new koa();

app.use(bodyparser());
app.use(async (ctx) => {
    const param = ctx.request.body;
    const validate = joi.validate({
        topic: 'news',
        message: 'sssd',
        options: {}
    }, joi.object({
        topic: joi.string().required(),
        message: joi.required(),
        options: {
            qos: joi.number(),
            retain: joi.boolean(),
            dup: joi.boolean()
        }
    }));

    if (ctx.method !== 'POST' || validate.error !== null) {
        ctx.body = {
            error: 1,
            msg: validate.error.details
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
