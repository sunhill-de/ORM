<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Properties\PropertyInteger;

class ORMObjectStoreTest extends TestCase
{

    public function testDummy()
    {
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);

        $test->dummyint = 123;        
        $test->commit();
        
        $this->assertEquals(123,$fake_storage->getEntity('dummyint'));
        $this->assertEquals(1, $test->getID());
        $this->assertEquals('2023-05-13 19:30:20', $test->updated_at);
        $this->assertEquals('abcdefghi',$fake_storage->uuid);
        $this->assertEquals(0, $fake_storage->getEntity('obj_owner'));
    }
    
    public function testTags()
    {
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        
        $tag1 = new Tag();
        $this->setProtectedProperty($tag1, 'tag_id', 1);
        $tag2 = new Tag();
        $this->setProtectedProperty($tag2, 'tag_id', 3);
        
        $test->dummyint = 123;
        $test->tags[] = $tag1;
        $test->tags[] = $tag2;
        $test->commit();
        
        $this->assertEquals([1,3],$fake_storage->getEntity('tags'));
    }
    
    public function testAttributes()
    {        
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        $int_property = new \StdClass();
        $int_property->name = 'int_attribute';
        $int_property->type = 'int';
        $int_property->id = 1;        
        
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        Attributes::shouldReceive('getAttributeForClass')->once()->andReturn($int_property);
        
        $test->int_attribute = 132;
        $test->dummyint = 123;
        $test->commit();
        
        $this->assertEquals(132,$fake_storage->getEntity('attributes')[0]->value);
    }
}