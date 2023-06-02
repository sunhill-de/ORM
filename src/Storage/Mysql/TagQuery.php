<?php

/**
 * @file TagQuery.php
 * Provides the TagQuery for querying tags
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-31
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Query\DBQuery;
use Illuminate\Support\Collection;

class TagQuery extends DBQuery
{
    protected $keys = [
        'id'=>'handleNumericField',
        'parent_id'=>'handleNumericField',
        'parent'=>'handleParent',
        'is assigned'=>'handleAssignment',
        'not assigned'=>'handleAssignment',
        'full_path'=>'handleFullpath',
        'name'=>'handleStringField',
        'any_path'=>'handleAnyPath'
    ];

    protected $fullpath_only = true;
    
    protected function getBasicTable()
    {
        return DB::table('tags');
    }
 
    protected function handleParent($connection, $key, $relation, $value)
    {
        $connection .= 'In';
        $subquery = DB::table('tags')->select('id')->where('name',$value);
        $this->query->$connection('tags.parent_id', $subquery);
        return $this;
    }
    
    protected function handleFullpath($connection, $key, $relation, $value)
    {
        if (is_null($value)) {
            $value = $relation;
            $relation = '=';
        }
        $this->query->$connection('path_name',$relation, $value);
    }
    
    protected function handleAnyPath($connection, $key, $relation, $value)
    {
        if (is_null($value)) {
            $value = $relation;
            $relation = '=';
        }
        $this->fullpath_only = false;
        $this->query->$connection('path_name',$relation, $value);
    }
    
    protected function handleAssignment($connection, $key, $relation, $value)
    {
        $this->query->leftJoin('tagobjectassigns','tags.id','=','tagobjectassigns.tag_id');
        if ($key == 'is assigned') {
            $this->query->whereNotNull('tagobjectassigns.container_id');
        } else {
            $this->query->whereNull('tagobjectassigns.container_id');            
        }
        $this->query->groupBy('tags.id');
    }
    
    protected function handleFullpathOnly()
    {
        if ($this->fullpath_only) {
            $this->query->join('tagcache','tagcache.tag_id','=','tags.id')->where('tagcache.is_fullpath',true)->select('tags.*','tagcache.path_name as fullpath');
        } else {
            $this->query->join('tagcache','tagcache.tag_id','=','tags.id')->select('tags.*','tagcache.path_name as fullpath');
        }        
    }
    
    public function get(): Collection
    {
        $this->handleFullpathOnly();
        return parent::get();
    }
    
    public function first(): \StdClass
    {
        $this->handleFullpathOnly();
        return parent::first();        
    }
    
    public function delete()
    {        
        $this->query->select('tags.id');
        DB::table('tagcache')->whereIn('tag_id',$this->query)->delete();
        DB::table('tagobjectassigns')->whereIn('tag_id',$this->query)->delete();
        $this->query->delete();
    }
    
    protected function updateCache(int $id)
    {
        DB::table('tagcache')->where('tag_id',$id)->delete();
        $current = $id;
        $tag = DB::table('tags')->where('id',$id)->first();
        $path = $tag->name;
        do {
            $current = $tag->parent_id;
            $is_fullpath = ($current == 0)?1:0;
            DB::table('tagcache')->insert(['path_name'=>$path,'tag_id'=>$id,'is_fullpath'=>$is_fullpath]);
            if ($current) {
                $tag = DB::table('tags')->where('id',$current)->first();
                $path = $tag->name.'.'.$path;
            }
        } while ($current);
    }
    
    protected function getRealupdate($fields)
    {
        $real_update = ['options'=>0];
        foreach ($fields as $name => $newvalue) {
            switch ($name) {
                case 'name':
                case 'parent_id':
                    $real_update[$name] = $newvalue;
                    break;
                case 'parent':
                    $real_update['parent_id'] = Tags::getTag($newvalue)->id;
                    break;
            }
        }
        return $real_update;
    }
    
    public function update($fields)
    {
        $this->query->update($this->getRealUpdate($fields));
        foreach ($this->query->get() as $entry) {
           $this->updateCache($entry->id); 
        }
    }
    
    public function insert($fields)
    {
        $id = DB::table('tags')->insertGetId($this->getRealUpdate($fields));
        $this->updateCache($id);
    }
    
    public function getTags()
    {
        return $this->get()->map(function(\StdClass $entry){
           return Tags::loadTag($entry->id); 
        });
    }
}