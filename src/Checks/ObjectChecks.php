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
        $master = $classes[$object]->table;
        $slave  = $classes[$classes[$object]->parent]->table;
        if ($result = $this->checkForDanglingPointers($master,'id',$slave,'id')) {
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
        $master = $classes[$table]->table;
        $slave  = $classes[$classes[$table]->parent]->table;
        return DB::table($master.' AS a')->leftJoin($slave.' AS b','a.id','=','b.id')->whereNull('b.id')->delete();    
    }
    
    protected function repair_EveryObjectHasAParentEntry(array $classes, array $missing)
    {
        foreach ($missing as $table => $count) {
            $this->repair_tableWithMissingParent($classes, $table);
        }
    }
    
     protected function getDistinctClasses()
    {
        $tables = DB::table('objects')->distinct('classname')->get();
        return $tables;
    }
    
    /**
     * Checks if all classes in objects exist
     * @return unknown
     */
    public function check_ObjectExistance(bool $repair) 
    {
        $classes = $this->getDistinctClasses();        
        $bad_classes = [];
        foreach ($classes as $class) {
            if (!Classes::searchClass($class->classname)) {
                $bad_classes[] = $class->classname;
            }
        }
        if (empty($bad_classes)) {
            $this->pass();
        } else {
            if ($repair) {
               DB::table('objects')->whereIn('classname', $bad_classes)->delete();                 
               $this->repair(__(":bad_classes non existing objects removed.",['bad_classes'=>count($bad_classes)]));
            } else {
                $this->fail(__(":bad_classes in objects don't exist.",['bad_classes'=>count($bad_classes)]));                
            }
        }
    }
    
}
