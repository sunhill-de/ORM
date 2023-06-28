<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertyList;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Objects\StorageInteraction\StorageInteractionBase;

class DummyPropertyCollection extends PropertiesCollection
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
 
    public function getIDName(): string
    {
        return 'id';
    }
    
    public function getIDType(): string
    {
        return 'int';
    }
    
    public static function search()
    {
        
    }
    
    protected function getUpdaterInteraction(): StorageInteractionBase
    {
        
    }
    
    protected function getStorerInteraction(): StorageInteractionBase
    {
        
    }
    
    protected function getLoaderInteraction(): StorageInteractionBase
    {
        
    }
 }
