<?php
namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertyList;

class AnotherDummyPropertyCollection extends DummyPropertyCollection
{
    
    protected static function setupInfos()
    {
        static::addInfo('name','AnotherDummyPropertyCollection');
        static::addInfo('test','This is another test', true);
        static::addInfo('something','else');
    }
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('anothertestint');
        $list->string('anotherteststring',10);
    }
    
}

