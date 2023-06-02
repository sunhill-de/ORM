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
namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Query\DBQuery;

class TagQuery extends DBQuery
{
    protected $keys = [
        'id'=>'handleNumericField',
        'parent_id'=>'handleNumericField',
        'parent'=>'handleParent',
        'is assigned'=>'handleAssignment',
        'not assigned'=>'handleAssignment',
        'full_path'=>'handeFullpath',
        'name'=>'handleStringField'
    ];
    
    protected function getBasicTable()
    {
        return DB::table('tags')->join('tagcache','tagcache.tag_id','=','tags.id')->where('tagcache.is_fullpath',true)->select('tags.*','tagcache.path_name as fullpath');
    }
 
    protected function handleParent($connection, $key, $relation, $value)
    {
        $subquery = DB::table('tags')->select('id')->where('name',$value);
        $this->query->whereIn('tags.parent_id', $subquery);
        return $this;
    }
    
    protected function handleFullpath($connection, $key, $relation, $value)
    {
        
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
    
    public function getTags()
    {
        return $this->get()->map(function(\StdClass $entry){
           return Tags::loadTag($entry->id); 
        });
    }
}