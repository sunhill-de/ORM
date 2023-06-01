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
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Query\DBQuery;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Query\NotAllowedRelationException;

class TagQuery extends DBQuery
{
    protected $keys = [
        'id'=>'handleNumeric',
        'parent_id'=>'handleNumeric',
        'handle'=>'handleParent',
        
    ];
    
    protected function getBasicTable()
    {
        return DB::table('tags');
    }
 
    protected function handleParent($relation, $value): BasicQuery
    {
        $subquery = DB::table('tags')->where('name',$value);
        $this->query->whereIn('id', $subquery);
        return $this;
    }
    
    protected function handleFullpath($relation, $value): BasicQuery
    {
        
    }
    
    protected function handleAssigned(bool $positive)
    {
        
    }
    
    public function where($key, $relation = null, $value = null): BasicQuery
    {
        switch ($key) {
            case 'parent':
                return $this->handleParent($relation, $value);
            case 'full_path':
                return $this->handleFullpath($relation, $value);
            case 'is assigned':
                return $this->handleAssigned(true);
            case 'not assigned':
                return $this->handleAssigned(false);
            case 'id':
            case 'parent_id':
                return $this->handleNumericFields($key, $relation, $value);
            case 'name':
                return $this->handleStringField($key, $relation, $value);
            default:
                throw new UnknownFieldException("Unknown key field in where condition: '$key'"); 
        }
    }
        
}