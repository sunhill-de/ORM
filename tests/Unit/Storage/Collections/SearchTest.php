<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class SearchTest extends DatabaseTestCase
{
    
    /**
     * @dataProvider CollectionSearchProvider
     * @param unknown $class
     * @param unknown $modifier
     * @param unknown $expect
     */
    
    public function testCollectionSearch($class, $modifier, $expect)
    {
        $collection = new $class();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $result = $modifier($test->dispatch('search'));
        
        $this->assertEquals($expect, $result);
        
    }
    
    public function CollectionSearchProvider()
    {
        return [
            [DummyCollection::class, function($query) { return $query->count(); }, 8] 
        ];
    }
}