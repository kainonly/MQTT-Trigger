<?php
declare (strict_types=1);

namespace think\aliyun\extra;

use think\Service;

class OssService extends Service
{
    public function register(): void
    {
        $this->app->bind(OssInterface::class, function () {
            $config = $this->app->config->get('aliyun');
            return new OssFactory($config);
        });
    }
}