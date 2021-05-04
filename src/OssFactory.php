<?php
declare (strict_types=1);

namespace think\aliyun\extra;

use Carbon\Carbon;
use Exception;
use OSS\OssClient;
use think\facade\Request;

class OssFactory implements OssInterface
{
    /**
     * 阿里云配置
     * @var array
     */
    private array $option;

    /**
     * 对象存储客户端
     * @var OssClient
     */
    private OssClient $client;

    /**
     * OssFactory constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 创建客户端
     * @param bool $extranet 是否为外网
     * @return OssClient
     * @throws Exception
     */
    private function setClient(bool $extranet): OssClient
    {
        if (!empty($this->client)) {
            return $this->client;
        }
        $this->client = new OssClient(
            $this->option['accessKeyId'],
            $this->option['accessKeySecret'],
            !$extranet ? $this->option['oss']['endpoint'] : $this->option['oss']['extranet']
        );
        return $this->client;
    }

    /**
     * @param bool $extranet
     * @return OssClient
     * @throws Exception
     * @inheritDoc
     */
    public function getClient(bool $extranet = false): OssClient
    {
        return $this->setClient($extranet);
    }

    /**
     * 上传对象
     * @param string $name 文件名称
     * @return string
     * @throws Exception
     */
    public function put(string $name): string
    {
        $file = Request::file($name);
        $fileName = date('Ymd') . '/' . uuid()->toString() . '.' . $file->getOriginalExtension();
        $client = $this->setClient(false);
        $client->uploadFile(
            $this->option['oss']['bucket'],
            $fileName,
            $file->getRealPath()
        );

        return $fileName;
    }

    /**
     * @param array $keys
     * @throws Exception
     * @inheritDoc
     */
    public function delete(array $keys): void
    {
        $client = $this->setClient(false);
        $client->deleteObjects(
            $this->option['oss']['bucket'],
            $keys
        );
    }

    /**
     * @param array $conditions
     * @param int $expired
     * @return array
     * @throws Exception
     * @inheritDoc
     */
    public function generatePostPresigned(array $conditions, int $expired = 600): array
    {
        $date = Carbon::now()->setTimezone('UTC');
        $filename = date('Ymd') . '/' . uuid()->toString();
        $policy = base64_encode(json_encode([
            'expiration' => $date->addSeconds($expired)->toISOString(),
            'conditions' => [
                ['bucket' => $this->option['oss']['bucket']],
                ['starts-with', '$key', $filename],
                ...$conditions
            ]
        ]));
        $signature = base64_encode(hash_hmac('sha1', $policy, $this->option['accessKeySecret'], true));
        return [
            'filename' => $filename,
            'type' => 'oss',
            'option' => [
                'access_key_id' => $this->option['accessKeyId'],
                'policy' => $policy,
                'signature' => $signature
            ],
        ];
    }
}