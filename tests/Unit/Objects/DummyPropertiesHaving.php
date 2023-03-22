<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertiesHaving;
use Sunhill\ORM\Objects\PropertiesHavingException;

class DummyPropertiesHaving extends PropertiesHaving
{
    
    protected static function setupInfos()
    {
        static::addInfo('name','DummyPropertiesHaving');
        static::addInfo('test', 'This is a test.', true);
    }
 
    public static function translate(string $value): string
    {
        return 'Trans:'.$value;    
    }
    
    public static function callStaticMethod(string $string, array $params = [], bool $clear = true)
    {
        if ($clear) {
            static::$property_definitions = [];
        }
        return static::$string(...$params);
    }
    
    public static function callMethod(string $method)
    {
        static::$property_definitions = [];
        return static::$method('test');
    }
}
