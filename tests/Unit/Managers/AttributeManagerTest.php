<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Attributes;
use Sunhill\ORM\Managers\Exceptions\InvalidAttributeIDException;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Managers\Exceptions\InvalidTypeException;
use Sunhill\ORM\Managers\Exceptions\NotAnORMClassException;

class AttributeManagerTest extends DatabaseTestCase
{

    public function testGetAvaiableAttributesForClass()
    {
        $this->assertEquals(5, count(Attributes::getAvaiableAttributesForClass(Dummy::class)));
        $this->assertEquals('text_attribute', Attributes::getAvaiableAttributesForClass(Dummy::class)[4]->name);
    }
    
    public function testGetAvaiableAttributesForClassWithFilter()
    {
        $this->assertEquals(4, count(Attributes::getAvaiableAttributesForClass(Dummy::class,['general_attribute'])));
    }
    
    public function testDeleteAttribute()
    {
        $this->assertDatabaseHasTable('attr_attribute1');
        $this->assertDatabaseHas('attributes',['id'=>2]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>2]);
        
        Attributes::deleteAttribute(2);
        
        $this->assertDatabaseMissingTable('attr_attribute1');
        $this->assertDatabaseMissing('attributes',['id'=>2]);
        $this->assertDatabaseMissing('attributeobjectassigns',['attribute_id'=>2]);        
    }
    
    public function testDeleteAttribute_fail()
    {
        $this->expectException(InvalidAttributeIDException::class);
        Attributes::deleteAttribute(10000);
    }
    
    public function testAddAttribute()
    {
        $id = Attributes::addAttribute('testaddattribute','string',['dummy','testparent']);
        
        $this->assertDatabaseHasTable('attr_testaddattribute');
        $this->assertDatabaseHas('attributes',['id'=>$id,'name'=>'testaddattribute','type'=>'string','allowed_classes'=>'|dummy|testparent|']);        
    }
    
    /**
     * @dataProvider AddAttributeProvider
     * @param unknown $name
     * @param unknown $type
     * @param unknown $classes
     * @param unknown $exception
     */
    public function testAddAttributeFailure($name, $type, $classes, $exception)
    {
        $this->expectException($exception);
        Attributes::addAttribute($name, $type, $classes);
    }
    
    public static function AddAttributeProvider()
    {
        return [
            ['_invalid','string',[],InvalidNameException::class],
            ['invälid','string',[],InvalidNameException::class],
            ['inv alid','string',[],InvalidNameException::class],
            ['invalid','object',[],InvalidTypeException::class],
            ['invalid','string',['dummy','Invalid'],NotAnORMClassException::class]
        ];
    }
    
    public function testUpdateAttribute_name()
    {
        Attributes::editAttribute(2,'changedattribute','integer',['testparent']);
        
        $this->assertDatabaseMissingTable('attr_attribute1');
        $this->assertDatabaseHasTable('attr_changedattribute');
        $this->assertDatabaseHas('attributes',['id'=>2,'name'=>'changedattribute','type'=>'integer','allowed_classes'=>'|testparent|']);
    }
}
