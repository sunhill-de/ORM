<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Tags;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\TagException;
use Sunhill\ORM\Storage\Mysql\TagQuery;

class MysqlTagsTest extends DatabaseTestCase
{

    public function testUpdateTagName() 
    {
        $test = new TagQuery();            
        $test->where('id',3)->update(['name'=>'NewTagC']);
        
        $this->assertEquals('NewTagC',Tags::getTag(3)->name);
        $this->assertEquals('TagB.NewTagC',Tags::getTag(3)->fullpath);
        
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[0]->path_name,'NewTagC');
    }
    
    public function testChangeTagParent() 
    {       
        $test = new TagQuery();
        $test->where('id',3)->update(['parent'=>'TagD']);
        
        $this->assertEquals('TagD.TagC',Tags::getTag(3)->fullpath);
    
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[1]->path_name,'TagD.TagC');
        $this->assertTrue(($result[1]->is_fullpath)?true:false);
        $this->assertEquals($result[0]->path_name,'TagC');
        $this->assertFalse(($result[0]->is_fullpath)?true:false);
    }
   
    public function testClearTags() 
    {
        $test = new TagQuery();
        $test->delete();

        $result = DB::table('tagcache')->get();
        $this->assertTrue($result->isEmpty());

        $result = DB::table('tagobjectassigns')->get();
        $this->assertTrue($result->isEmpty());

        $result = DB::table('tags')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    public function testDeleteTag()
    {
        $test = new TagQuery();
        $test->where('id',3)->delete();
        
        $exception = false;
        try {
            Tags::getTag(3);
        } catch (TagException $e) {
                $exception = true;
        }
        $this->assertTrue($exception, "The expected TagNotFound exception was not raised.");
        $this->assertEquals('TagA',Tags::getTag(1)->name);

        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
        
        $result = DB::table('tagobjectassigns')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
    
    public function testAddTag()
    {
        $test = new TagQuery();
        $test->insert(['name'=>'test','parent'=>'TagA','options'=>0]);
        
        $query = DB::table('tags')->where('name','test')->first();
        $this->assertFalse(empty($query));        
        $id = $query->id;
        
        $query = DB::table('tagcache')->where('path_name','test')->first();
        $this->assertEquals($id, $query->tag_id);
        $this->assertEquals(0,$query->is_fullpath);

        $query = DB::table('tagcache')->where('path_name','TagA.test')->first();
        $this->assertEquals($id, $query->tag_id);
        $this->assertEquals(1,$query->is_fullpath);        
    }
    
    /**
     * @dataProvider QueryProvider
     * @group query
     */
    public function testQuery($callback, $modifier, $expect)
    {
        $query = Tags::query();
        $result = $callback($query);
        
        if (is_callable($modifier)) {
            $result = $modifier($result);
        }
        $this->assertEquals($expect, $result);
    }
    
    public function QueryProvider()
    {
        return [
            [function($query) { return $query->count(); }, null, 9],
            [function($query) { return $query->first(); }, function($value){ return $value->name; }, 'TagA'],
            [function($query) { return $query->offset(5)->first(); }, function($value) { return $value->name; }, 'TagF'],
            [function($query) { return $query->orderBy('name')->offset(5)->first(); }, function($value) { return $value->name; }, 'TagE'],
            [function($query) { return $query->where('name','TagC')->first(); }, function($value){ return $value->name; }, 'TagC'],
            [function($query) { return $query->where('parent_id','<>',0)->first(); }, function($value){ return $value->name; }, 'TagC'],
            [function($query) { return $query->where('parent','=','TagF')->get(); }, function($value){ return $value[0]->name; }, 'TagG'],
            [function($query) { return $query->where('is assigned')->get(); }, function($value){ return $value[0]->name; }, 'TagA'],
            [function($query) { return $query->where('not assigned')->get(); }, function($value){ return $value[2]->name; }, 'TagZ'],
            ];
    }
    
    
}