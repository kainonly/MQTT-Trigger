<?php
declare (strict_types=1);

namespace think\aliyun\extra\service;

use think\aliyun\extra\common\OssFactory;
use think\extra\contract\UtilsInterface;
use think\Service;

class OssService extends Service
{
    public function register()
    {
        $this->app->bind('oss', function () {
            $config = $this->app->config
                ->get('aliyun');
            $utils = $this->app->get(UtilsInterface::class);
            return new OssFactory($config, $utils);
        });
    }
}