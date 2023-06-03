<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Tags;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\TagException;
use Sunhill\ORM\Storage\Mysql\TagQuery;
use Sunhill\ORM\Storage\Mysql\AttributeQuery;
use Sunhill\ORM\Managers\AttributeInvalidTypeException;

class MysqlAttributesTest extends DatabaseTestCase
{

    public function testUpdateAttributeName()
    {
        $test = new AttributeQuery();
        
        $this->assertDatabaseHasTable('attr_int_attribute');
        $this->assertDatabaseMissingTable('attr_newname');
        
        $test->where('id',1)->update(['name'=>'newname']);

        $this->assertDatabaseHas('attributes',['id'=>1,'name'=>'newname']);
        $this->assertDatabaseMissingTable('attr_int_attribute');
        $this->assertDatabaseHasTable('attr_newname');
    }
    
    public function testUpdateAttributeType()
    {
        $test = new AttributeQuery();
        
        $this->assertDatabaseHas('attributes',['id'=>1,'type'=>'integer']);
        $this->assertEquals('integer',DB::getSchemaBuilder()->getColumnType('attr_int_attribute', 'value'));
        $test->where('id',1)->update(['type'=>'String']);
        
        $this->assertDatabaseHas('attributes',['id'=>1,'type'=>'string']);
        $this->assertEquals('string',DB::getSchemaBuilder()->getColumnType('attr_int_attribute', 'value'));
    }
    
    public function testUpdateAttrubuteWrongType()
    {
        $this->expectException(AttributeInvalidTypeException::class);
        
        $test = new AttributeQuery();
        $test->where('id',1)->update(['type'=>'unknown']);
    }
    
    public function testUpdateAllowedClasses()
    {
        $test = new AttributeQuery();

        $this->assertDatabaseHas('attributes',['id'=>1,'allowed_classes'=>'|dummy|']);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>1,'object_id'=>4]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>1,'object_id'=>8]);
        $test->where('id',1)->update(['allowed_classes'=>['dummychild']]);

        $this->assertDatabaseMissing('attributeobjectassigns',['attribute_id'=>1,'object_id'=>4]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>1,'object_id'=>8]);
        $this->assertDatabaseHas('attributes',['id'=>1,'allowed_classes'=>'|dummychild|']);        
    }
    
    public function testDeleteAttribute()
    {
        $test = new AttributeQuery();
        
        $test->where('id',1)->delete();
        
        $this->assertDatabaseMissing('attributes',['name'=>'int_attribute']);
        $this->assertDatabaseMissing('attributeobjectassigns',['attribute_id'=>1]);
        $this->assertDatabaseMissingTable('attr_int_attribute');
    }
    
    /**
     * @dataProvider SimpleQueriesProvider
     */
    public function testSimpleQueries($modifier, $expect)
    {
        $test = new AttributeQuery();
        
        $this->assertEquals($expect, $modifier($test));
    }
    
    public function SimpleQueriesProvider()
    {
        return [
            [function($query) { return $query->count(); }, 9],
            [function($query) { $result = $query->first(); return $result->name; }, 'int_attribute'],
            [function($query) { $result = $query->get(); return $result[0]->name; }, 'int_attribute'],
            [function($query) { 
                $result = $query->where('name','attribute1')->first(); 
                return $result->type; 
            }, 'integer'],
            [function($query) {
                $result = $query->orderBy('name')->first();
                return $result->name;
            }, 'attribute1'],
            [function($query) {
                $result = $query->where('allowed_classes','matches','testparent')->first();
                return $result->name;
            }, 'attribute1'],
            [function($query) {
                return $query->where('allowed_classes','matches','testparent')->count();
            }, 3],
            [function($query) {
                return $query->where('allowed_classes','matches','dummy')->count();
            }, 5],
            [function($query) {
                $result = $query->where('assigned')->get();
                return count($result);
            }, 8],
            [function($query) {
                $result = $query->whereNot('assigned')->get();
                return count($result);
            }, 1],
            ];
    }
}