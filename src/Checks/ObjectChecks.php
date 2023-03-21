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
use Illuminate\Support\Facades\Schema;
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
     * Checks for a single object table if the entries in the parent table exists
     * @param array $classes
     * @param string $object
     * @param array $missing
     */
    protected function processTable(array $classes, string $object, array &$missing)
    {
        if ($object == 'object') {
            return;
        }
        if ($result = $this->checkForDanglingPointers($classes[$object]['table'],'id',$classes[$classes[$object]['parent']]['table'],'id')) {
            $missing[$object] = $result;
        } 
    }
    
    /**
     * Checks if every entry in every object table has a according entry in the parent object table
     * @param bool $repair
     * Test: 
     */
    public function check_EveryObjectHasAParentEntry(bool $repair)
    {
        $missing = [];
        $classes = Classes::getAllClasses();
        foreach ($classes as $class => $info) {
            $this->processTable($classes, $class, $missing);
        }
        if (empty($missing)) {
            $this->pass();
        } else {
            if ($repair) {
                $this->repair_EveryObjectHasAParentEntry($classes,$missing);
                $this->repair(__(":count tables with a missing parent entry fixed",['count'=>count($missing)]));
            } else {
                $this->fail(__(":count tables have a missing parent entry",['count'=>count($missing)]));
            }
        }
    }

    protected function repair_tableWithMissingParent(array $classes, string $table)
    {
        $master = $classes[$table]['table'];
        $slave  = $classes[$classes[$table]['parent']]['table'];
        return DB::table($master.' AS a')->leftJoin($slave.' AS b','a.id','=','b.id')->whereNull('b.id')->delete();    
    }
    
    protected function repair_EveryObjectHasAParentEntry(array $classes, array $missing)
    {
        foreach ($missing as $table => $count) {
            $this->repair_tableWithMissingParent($classes, $table);
        }
    }
    
    /**
     * Checks if all container objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_ObjectObjectAssignsContainerExist(bool $repair) 
    {
        if ($entries = $this->checkForDanglingPointers('objectobjectassigns','container_id','objects','id',true)) {
            if ($repair) {
                $entries = $this->repairDanglingPointers('objectobjectassigns','container_id','objects','id');                
                $this->repair(__(':entries container objects are missing in the objectobjectassigns table',['entries'=>$entries]));
            } else {
               $this->fail(__(':entries container objects are missing in the objectobjectassigns table',['entries'=>$entries]));   
            }
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all container objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_ObjectObjectAssignsElementExist(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('objectobjectassigns','element_id','objects','id',true)) {
            if ($repair) {
                $entries = $this->repairDanglingPointers('objectobjectassigns','element_id','objects','id');
                $this->repair(__(':entries element objects are missing in the objectobjectassigns table',['entries'=>$entries]));
            } else {
                $this->fail(__(':entries element objects are missing in the objectobjectassigns table',['entries'=>$entries]));
            }
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
        $query = Schema::getAllTables();
        foreach ($query as $found_table) {
            if ($table == $found_table->name) {
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
