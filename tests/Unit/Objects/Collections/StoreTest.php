<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Unit\Objects\DummyStorage;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\PropertyMap;

class StoreTest extends DatabaseTestCase
{
    
    /**
     * @group storecollection
     */
    public function testDummyCollectionStore()
    {
        $test = new DummyCollection();
        $storage = new DummyStorage();
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->dummyint = 988;
        $test->commit();
        
        $this->assertEquals('stored', $storage->state);
        $this->assertEquals(988, $storage->dummyint);
    }
    
}