# MQTT-Trigger

MQTT HTTP Proxy Trigger

#### Clone Project

```shell
# git clone https://github.com/kainonly/mqtt-trigger
```

#### Set Env

```ini
uri = mqtt://test.mosquitto.org
port = 3000
```

- `uri` MQTT connect url
- `port` HTTP Listen Port

#### Trigger

```shell
curl -X POST \
  http://localhost:3000 \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{
	"topic":"erp.order.create",
	"message":"L2-ccq123456",
	"options":{}
}'
``` 

Only post requests are supported, `body` like

- `topic: string` topic name
- `message: any` message
- `options`
  - `qos: number` qos
  - `retain: boolean` retain
  - `dup: boolean` dup
