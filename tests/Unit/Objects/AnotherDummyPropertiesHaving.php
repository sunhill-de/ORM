<?php
namespace Sunhill\ORM\Tests\Unit\Objects;

class AnotherDummyPropertiesHaving extends DummyPropertiesHaving
{
    
    protected static function setupInfos()
    {
        static::addInfo('name','AnotherDummyPropertiesHaving');
        static::addInfo('test','This is another test', true);
        static::addInfo('something','else');
    }
    
    protected static function setupProperties()
    {
        static::integer('test');
    }

    public static function callAddProperty()
    {
        return static::integer('test');
    }
    
}

