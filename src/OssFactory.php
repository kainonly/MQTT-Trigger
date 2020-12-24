<?php
declare (strict_types=1);

namespace think\aliyun\extra;

use Carbon\Carbon;
use Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use think\facade\Request;

/**
 * 对象存储处理类
 * Class OssFactory
 * @package think\aliyun\extra\common
 */
class OssFactory
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
     * @throws OssException
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
     * 获取对象存储客户端
     * @param bool $extranet
     * @return OssClient
     * @throws OssException
     */
    public function getClient(bool $extranet = false): OssClient
    {
        return $this->setClient($extranet);
    }

    /**
     * 上传至对象存储
     * @param string $name 文件名称
     * @return string
     * @throws Exception
     */
    public function put(string $name): string
    {
        $file = Request::file($name);
        $fileName = date('Ymd') . '/' .
            uuid()->toString() . '.' .
            $file->getOriginalExtension();

        $client = $this->setClient(false);
        $client->uploadFile(
            $this->option['oss']['bucket'],
            $fileName,
            $file->getRealPath()
        );

        return $fileName;
    }

    /**
     * 生成客户端上传OSS对象存储签名
     * @param array $conditions 表单域的合法值
     * @param int $expired 过期时间
     * @return array
     * @throws Exception
     */
    public function generatePostPresigned(array $conditions, int $expired = 600): array
    {
        $date = Carbon::now()->setTimezone('UTC');
        $policy = base64_encode(json_encode([
            'expiration' => $date->addSeconds($expired)->toISOString(),
            'conditions' => $conditions
        ]));
        $signature = base64_encode(hash_hmac('sha1', $policy, $this->option['accessKeySecret'], true));
        return [
            'filename' => date('Ymd') . '/' . uuid()->toString(),
            'type' => 'oss',
            'option' => [
                'access_key_id' => $this->option['accessKeyId'],
                'policy' => $policy,
                'signature' => $signature
            ],
        ];
    }
}