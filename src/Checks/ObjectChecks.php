<?php

/**
 * @file OrmChecks.php
 * An extension to the sunhill check system to perform checks on the sunhill orm database
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-09-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/ORMCheckTest.php
 * Coverage: unknown
 * PSR-Status: complete
 */

namespace Sunhill\ORM\Checks;

use Sunhill\Basic\Checker\Checker;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\Basic\Utils\Descriptor;

/**
 * Provides checks for the checking subsystem of sunhill for the orm system
 * @author klaus
 *
 */
class ObjectChecks extends ChecksBase 
{
    
    /**
     * Checks if all container objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_ObjectObjectAssignsContainerExist(bool $repair) 
    {
        if ($entries = $this->checkForDanglingPointers('objectobjectassigns','container_id','objects','id',true)) {
            $this->fail(__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all element objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_ObjectObjectAssignsElementExist(bool $repair) 
    {
        if ($entries = $this->checkForDanglingPointers('objectobjectassigns','element_id','objects','id',true)) {
            $this->fail(__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all container objects in the stringobjectassigns table exists
     * @return unknown
     */
    public function check_StringObjectAssignsContainerExist(bool $repair) 
    {
        if ($entries = $this->checkForDanglingPointers('stringobjectassigns','container_id','objects','id',true)) {
            $this->fail(__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all classes in objects exist
     * @return unknown
     */
    public function check_ObjectExistance(bool $repair) 
    {
        $tables = DB::table('objects')->distinct('classname')->get();
        $bad_classes = '';
        foreach ($tables as $table) {
            if (!Classes::searchClass($table->classname)) {
                $bad_classes .= (empty($bad_classes)?'':', ').$table->classname;
            }
        }
        if (empty($bad_classes)) {
            $this->pass();
        } else {
            $this->fail(__("Classes ':bad_classes' dont exist.",['bad_classes'=>$bad_classes]));            
        }
    }
    
    public function check_ClassTableGaps(bool $repair) 
    {
        $table_tree = $this->getTableTree();
        $return = '';
        
        foreach ($table_tree as $master=>$table) {
            if ($result = $this->testTable($master,$table)) {
                $return .= (empty($return)?'':',').$result;        
            }
        }
        
        if (empty($return)) {
            $this->pass();
        } else {
            $this->fail(__("Objects ':return' have gaps.",['return'=>$return]));
        }
    }
    
    private function tableExists(string $table): bool 
    {
        $query = DB::select("show tables");
        foreach ($query as $found_table) {
            if ($table == $found_table->Tables_in_sunhill) {
                return true;
            }
        }
        return false;
    }
    
    private function testTable(string $master,array $tables) 
    {
        if (!$this->tableExists($master)) {
            return;
        }
        $query = DB::table($master);
        foreach ($tables as $table) {
            $query = $query->leftJoin($table,$table.'.id','=',$master.'.id');
            $query = $query->orWhere("$table.id",null);
        }
        $result = $query->get();
        if ($result->count() > 0) {
            return $master;
        }
    }
    
    private function getSubtables(string $class): array 
    {        
        $parent_classes = Classes::getInheritanceOfClass($class);
        $result = [];
        
        foreach ($parent_classes as $parent) {
            $result[] = Classes::getTableOfClass($parent);
        }
        
        return $result;
    }
    
    private function getTableTree() 
    {
        $classes = Classes::getAllClasses();
        $tables = [];
        foreach ($classes as $class) {
            if ($class['name'] !== 'object') {
                $tables[Classes::getTableOfClass($class['name'])] = $this->getSubtables($class['name']);
            }
        }
        
        return $tables;
    }
}
