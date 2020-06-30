<?php
declare (strict_types=1);

namespace think\aliyun\extra\service;

use think\aliyun\extra\common\OssFactory;
use think\Service;

class OssService extends Service
{
    public function register(): void
    {
        $this->app->bind('oss', function () {
            $config = $this->app->config
                ->get('aliyun');
            return new OssFactory($config);
        });
    }
}