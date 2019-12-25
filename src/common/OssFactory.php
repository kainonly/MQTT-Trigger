<?php
declare (strict_types=1);

namespace think\aliyun\extra\common;

use Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use think\facade\Request;
use think\extra\contract\UtilsInterface;

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
    private $option;

    /**
     * 工具
     * @var UtilsInterface
     */
    private $utils;

    /**
     * 对象存储客户端
     * @var OssClient
     */
    private $client;

    /**
     * OssFactory constructor.
     * @param array $option
     * @param object $utils
     * @throws OssException
     */
    public function __construct(array $option, UtilsInterface $utils)
    {
        $this->option = $option;
        $this->utils = $utils;
    }

    /**
     * 创建客户端
     * @return OssClient
     * @throws OssException
     */
    private function setClient(): OssClient
    {
        if (!empty($this->client)) {
            return $this->client;
        }
        $this->client = new OssClient(
            $this->option['accessKeyId'],
            $this->option['accessKeySecret'],
            $this->option['oss']['endpoint']
        );
        return $this->client;
    }

    /**
     * 获取对象存储客户端
     * @return OssClient
     */
    public function getClient(): OssClient
    {
        return $this->client;
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
            $this->utils->uuid()->toString() . '.' .
            $file->getOriginalExtension();

        $client = $this->setClient();
        $client->uploadFile(
            $this->option['oss']['bucket'],
            $fileName,
            $file->getRealPath()
        );

        return $fileName;
    }
}