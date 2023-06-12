<?php

/**
 * @file AttributeQuery.php
 * Provides the AttributeQuery for querying attributes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-03
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Query\DBQuery;
use Illuminate\Support\Collection;
use Sunhill\ORM\Managers\AttributeInvalidTypeException;
use Sunhill\ORM\Managers\AttributeInvalidColumnException;
use Sunhill\ORM\Query\NotAllowedRelationException;

class MysqlAttributeQuery extends DBQuery
{
    protected $keys = [
        'id'=>'handleNumericField',
        'name'=>'handleStringField',
        'allowed_classes'=>'handleAllowedClasses',
        'assigned'=>'handleAssigned'
    ];

    protected function getBasicTable()
    {
        return DB::table('attributes');
    }
 
    protected function buildChildList($target)
    {
        $list = Classes::getChildrenOfClass($target,1);
        $result = array_keys($list);
        foreach ($list as $entry => $info) {
            $result = array_merge($result, $this->buildChildList($entry));
        }
        return $result;
    }
    
    protected function handleAssigned($connection, $key, $relation, $value)
    {
        $this->query->leftJoin('attributeobjectassigns','attributes.id','=','attributeobjectassigns.attribute_id');
        switch ($connection) {
            case 'where':
                $connection = 'whereNotNull'; break;
            case 'whereNot':
                $connection = 'whereNull'; break;
            case 'orWhere':
                $connection = 'orWhereNotNull'; break;
            case 'orWhereNot':
                $connection = 'orWhereNull'; break;
        }
        $this->query->$connection('attributeobjectassigns.object_id')->groupBy('attributes.id'); 
    }
    
    protected function handleAllowedClasses($connection, $key, $relation, $value)
    {
        if ($relation == 'matches') {
            if (is_string($value)) {
                $ancestors = Classes::getInheritanceOfClass($value, true);
            } else if (is_a($value, ORMObject::class)) {
                $ancestors = Classes::getInheritanceOfClass($value::getInfo('name'));
            }
            $this->query->$connection(function($builder) use ($ancestors) {
                foreach ($ancestors as $child) {
                    $builder->orWhere('allowed_classes','like','%|'.$child.'|%');
                }
            });
            return;
        }
        throw new NotAllowedRelationException("The operator '$relation' is not allowed in this context.");
    }
    
    protected function getRealUpdate(array $input): array
    {
        $result = [];
        
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'name':
                    $result[$key] = $value;
                    break;
                case 'allowed_classes':
                    $result[$key] = '|'.implode('|',$value).'|';
                    break;
                case 'type':
                    $value = strtolower($value);
                    if (!in_array($value,['integer','string','float','boolean','date','datetime','time','text'])) {
                        throw new AttributeInvalidTypeException("The type '$value' is not a valid type.");
                    }
                    $result[$key] = $value;
                    break;
                default:
                    throw new AttributeInvalidColumnException("The column '$key' does not exist.");
            }
        }
        
        return $result;
    }
    
    protected function updateName(int $id, string $from, string $to)
    {
        Schema::rename('attr_'.$from, 'attr_'.$to);
    }
    
    protected function updateType(int $id, string $attribute, string $to)
    {
        Schema::table('attr_'.$attribute, function ($table) use ($to) {
            $table->$to('value')->change();
        });
    }

    protected function classMatches($test, $child) 
    {
        if ($test == $child) {
            return true;
        }
        $children = Classes::getChildrenOfClass($child,1);
        foreach ($children as $child) {
            if ($this->classMatches($test, $child)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Helper method that checks if $test is a (sub)class of any of $candidates
     * 
     * @param unknown $test
     * @param array $candidates
     * @return bool
     */
    protected function classMatchesAny($test, array $candidates): bool
    {
        foreach ($candidates as $candidate) {
            if ($this->classMatches($test, $candidate)) {
                return true;
            }
        }
        return false;    
    }
    
    protected function updateAllowedClasses(int $id, array $to)
    {
        $distinct_classes = DB::table('attributeobjectassigns')->join('objects','attributeobjectassigns.object_id','=','objects.id')->where('attributeobjectassigns.attribute_id',$id)->select('classname')->distinct()->get();
        foreach ($distinct_classes as $class) {
            if (!$this->classMatchesAny($class->classname, $to)) {
                DB::table('attributeobjectassigns')->join('objects','attributeobjectassigns.object_id','=','objects.id')->where('attributeobjectassigns.attribute_id',$id)->where('objects.classname',$class->classname)->delete();
            }
        }
    }
    
    public function update($fields)
    {
        $old_values = $this->query->get();
        
        $real_fields = $this->getRealUpdate($fields);
        $this->query->update($real_fields);
        foreach ($old_values as $entry) {
            if (isset($real_fields['name'])) {
                $this->updateName($entry->id, $entry->name, $real_fields['name']);
            }
            if (isset($real_fields['type'])) {
                $this->updateType($entry->id, $entry->name, $real_fields['type']);
            }
            if (isset($real_fields['allowed_classes'])) {
                $this->updateAllowedClasses($entry->id, $fields['allowed_classes']);
            }
        }
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
    
    public function insert($fields)
    {
        $id = DB::table('attributes')->insertGetId($this->getRealUpdate($fields));
        return $id;
    }
    
    public function delete()
    {
        $entries = $this->query->get();
        DB::table('attributeobjectassigns')->whereIn('attribute_id', $this->query->select('id'))->delete();
        foreach ($entries as $entry) {
            Schema::drop('attr_'.$entry->name);
        }
        $this->query->delete();
    }
    
}