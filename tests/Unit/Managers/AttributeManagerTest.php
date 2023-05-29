<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Properties\PropertyException;

class AttributeManagerTest extends DatabaseTestCase
{

    public function testGetAllAttributes()
    {
        $list = array_map(function($element) { return $element->name; }, Attributes::getAllAttributes()->toArray());
        $this->assertTrue(in_array('int_attribute',$list));
    }
    
    public function testGetAllAttributesWithOffset()
    {
        $list = array_map(function($element) { return $element->name; }, Attributes::getAllAttributes(2,2)->toArray());
        $this->assertEquals(['attribute2','general_attribute'],$list);        
    }
    
    public function testGetCount()
    {
        $this->assertEquals(8, Attributes::getCount());
    }
    
    public function testGetAttribute()
    {
        $this->assertEquals('attribute1',Attributes::getAttribute(2)->name);
    }
    
    public function testAddAttribute()
    {
        Attributes::addAttribute('test_attribute','integer','dummy');
        $this->assertDatabaseHas('attributes',['name'=>'test_attribute']);
    }
    
    public function testUpdateAttribute()
    {
        Attributes::updateAttribute(1,'test_attribute','integer','dummy');
        $this->assertDatabaseHas('attributes',['id'=>1,'name'=>'test_attribute']);
    }
    
    public function testDeleteAttribute()
    {
        $this->assertDatabaseHas('attributes',['id'=>1]);
        $this->assertDatabaseHasTable('attr_int_attribute');
        Attributes::deleteAttribute(1);
        $this->assertDatabaseMissing('attributes',['id'=>1]);
        $this->assertDatabaseMissingTable('attr_int_attribute');
    }
    
    public function testGetAssociatedObjectsCount()
    {
        $this->assertEquals(2,Attributes::getAssociatedObjectsCount(1));
    }
    
    public function testGetAssociatedObjects()
    {
        $this->assertEquals(4, Attributes::getAssociatedObjects(1)[0]->id);
    }
    
    public function testGetAvaiableAttributesForClass()
    {
        $this->assertEquals(5, count(Attributes::getAvaiableAttributesForClass(Dummy::class)));
        $this->assertEquals('text_attribute', Attributes::getAvaiableAttributesForClass(Dummy::class)[4]->name);
    }
    
    public function testGetAvaiableAttributesForClassWithFilter()
    {
        $this->assertEquals(4, count(Attributes::getAvaiableAttributesForClass(Dummy::class,['general_attribute'])));
    }
    
    public function testGetAttributeForClass()
    {
        $attribute = Attributes::getAttributeForClass(Dummy::class, 'int_attribute');
        $this->assertEquals('int_attribute',$attribute->name);
    }
    
    public function testGetAttributeForClass_notexisting()
    {
        $this->expectException(PropertyException::class);
        $attribute = Attributes::getAttributeForClass(Dummy::class, 'nonexisting');
    }
    
    public function testGetAttributeForClass_notallowed()
    {
        $this->expectException(PropertyException::class);
        $attribute = Attributes::getAttributeForClass(Dummy::class, 'attribute1');
    }
    
    public function testGetAttributeType()
    {
        $this->assertEquals('integer',Attributes::getAttributeType('int_attribute'));
    }   
}
