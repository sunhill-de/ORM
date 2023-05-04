<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertyCollection;
use Sunhill\ORM\Objects\PropertyCollectionException;

class DummyPropertyCollection extends PropertyCollection
{
    
    protected static function setupInfos()
    {
        static::addInfo('name','DummyPropertyCollection');
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
    
    public static function callAddProperty()
    {
        return static::integer('test');
    }
}
