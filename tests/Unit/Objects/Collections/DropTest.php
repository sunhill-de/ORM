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

class DropTest extends DatabaseTestCase
{
    
    public function testDummyCollectionDelete()
    {
        $test = new DummyCollection();
        $storage = $this->mock(DummyStorage::class, function ($mock) {
           $mock->shouldReceive('delete')->once();
           $mock->shouldReceive('setType')->once();
           $mock->shouldReceive('setEntity')->once();
        });
        $storage->setType(DummyCollection::class);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->delete(1);
        
    }
    
    public function testComplexCollectionLoading()
    {
        $test = new ComplexCollection();
        $storage = new DummyStorage(ComplexCollection::class);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->delete(1);

        $this->assertEquals('deleted',$storage->state);
        $this->assertTrue($storage->hasEntity('field_int'));
    }

}