<?php

namespace think\aliyun\extra\facade;

use think\Facade;

/**
 * Class Oss
 * @method static string put(string $name)
 * @package think\aliyun\extra\facade
 */
final class Oss extends Facade
{
    protected static function getFacadeClass()
    {
        return 'oss';
    }
}