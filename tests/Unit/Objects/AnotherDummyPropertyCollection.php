<?php
namespace Sunhill\ORM\Tests\Unit\Objects;

class AnotherDummyPropertyCollection extends DummyPropertyCollection
{
    
    protected static function setupInfos()
    {
        static::addInfo('name','AnotherDummyPropertyCollection');
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

