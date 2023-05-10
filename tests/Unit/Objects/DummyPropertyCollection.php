<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertyCollection;
use Sunhill\ORM\Objects\PropertyCollectionException;
use Sunhill\ORM\Objects\PropertyList;

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
 
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('testint');
        $list->string('teststring',10);
    }
    
 }
