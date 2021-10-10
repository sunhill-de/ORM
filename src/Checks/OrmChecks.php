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

use Sunhill\Basic\Checker\checker;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;

/**
 * Provides checks for the checking subsystem of sunhill for the orm system
 * @author klaus
 *
 */
class OrmChecks extends Checker 
{
    
    /**
     * Helper function for the check for tables that point to non existing entries
     */
    protected function checkForDanglingPointers(string $master, string $master_field, string $slave, string $slave_field, bool $master_can_be_null=false) 
    {
        $query = DB::table($master.' AS a')->select('a.'.$master_field.' as id')->leftJoin($slave.' AS b','a.'.$master_field,'=','b.'.$slave_field)->whereNull('b.'.$slave_field);    
        if ($master_can_be_null) {
            $query = $query->where('a.'.$master_field,'>',0);
        }
        $query_result = $query->get();
        if (count($query_result)) {
            $result = '';
            foreach ($query_result as $entry) {
                $result .= (empty($result)?$entry->$slave_field:','.$entry->$slave_field);
            }
            return $result;
        } else {
            return null;
        }
    }
    
    /**
     * Checks if all tags have existing or no parents at all
     * @return unknown
     */
    public function check_TagsWithNotExistinPparents(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('tags','parent_id','tags','id',true)) {
            return $this->createResult(__('FAILED'),__('Check tags for not existing parents'),__("Parents of tags ':entries' dont exist.",['entries'=>$entries]));            
        } else {
            return $this->createResult(__('OK'),__('Check tags for not existing parents'));            
        }
    }
    
    /**
     * Checks if all entries in the tagcache have an existing tag
     * @return unknown
     */
    public function check_TagCacheWithNotExistingTags(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('tagcache','tag_id','tags','id')) {
            return $this->createResult(__('FAILED'),__("Check tagcache for not existing tags"),__("Tags ':entries' dont exist.",['entries'=>$entries]));            
        } else {
            return $this->createResult(__('OK'),__('Check tagcache for not existing tags'));
            
        }        
    }
    
    private function getTag(array $tags,int $id) 
    {
        foreach ($tags as $tag) {
            if ($tag->id == $id) {
                return $tag;
            }
        }
        return null;
    }
    
    private function buildTagRow(&$result, $tags, $tag, $postfix='') 
    {
            $result[] = $tag->name.$postfix;
            if ($newtag = $this->getTag($tags,$tag->parent_id)) {
                $this->buildTagRow($result,$tags,$newtag,'.'.$tag->name.$postfix);
            }
    }
    
    private function buildCache(&$result, $tags) 
    {
        foreach ($tags as $tag) {
            $this->buildTagRow($result,$tags,$tag);
        }
    }
    
    /**
     * Checks if the number of entries in the tagcache is correct and if all entries in the tagcache are right 
     * @return unknown
     */
    public function check_TagCacheConsistency(): Descriptor 
    {
        $tags = DB::table('tags')->get();
        $result = [];        
        $this->buildCache($result,$tags);
        $count = DB::table('tagcache')->count();
        if ($count !== count($result)) {
            return $this->createResult(__('FAILED'),__('Check tagcache consitency'),__("Entry count :count doenst match expected :expect",['count'=>$count,'expect'=>count($result)]));            
        }
        $tagcache_entries = DB::table('tagcache')->get();
        $entries = '';
        foreach ($tagcache_entries as $entry) {
            if (!in_array($entry->name,$result)) {
                $entries .= (empty($entries)?$entry->name:','.$entry->name);
            }
        }
        if (empty($entries)) {
            return $this->createResult(__('OK'),__('Check tagcache consitency'));            
        } else {
            return $this->createResult(__('FAILED'),__('Check tagcache consitency'),__("Entries :entries don't match.",['entries'=>$entries]));            
        }
    }
    
    /**
     * Checks if all tags in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_TagObjectAssignsTagsExist(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('tagobjectassigns','tag_id','tags','id',true)) {
            return $this->createResult(__('FAILED'),__('Check tag-object-assigns for not existing tags'),__("Tags ':entries' dont exist.",['entries'=>$entries]));
        } else {
            return $this->createResult(__('OK'),__('Check tag-object-assigns for not existing tags'));
        }
    }
    
    /**
     * Checks if all objects in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_TagObjectAssignsObjectsExist(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('tagobjectassigns','container_id','objects','id',true)) {
            return $this->createResult(__('FAILED'),__('Check tag-object-assigns for not existing objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->createResult(__('OK'),__('Check tag-object-assigns for not existing objects'));
        }
    }
    
    /**
     * Checks if all container objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_ObjectObjectAssignsContainerExist(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('objectobjectassigns','container_id','objects','id',true)) {
            return $this->createResult(__('FAILED'),__('Check object-object-assigns for not existing container objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->createResult(__('OK'),__('Check object-object-assigns for not existing container objects'));
        }
    }
    
    /**
     * Checks if all element objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_ObjectObjectAssignsElementExist(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('objectobjectassigns','element_id','objects','id',true)) {
            return $this->createResult(__('FAILED'),__('Check object-object-assigns for not existing element objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->createResult(__('OK'),__('Check object-object-assigns for not existing element objects'));
        }
    }
    
    /**
     * Checks if all container objects in the stringobjectassigns table exists
     * @return unknown
     */
    public function check_StringObjectAssignsContainerExist(): Descriptor 
    {
        if ($entries = $this->checkForDanglingPointers('stringobjectassigns','container_id','objects','id',true)) {
            return $this->createResult(__('FAILED'),__('Check string-object-assigns for not existing container objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->createResult(__('OK'),__('Check string-object-assigns for not existing container objects'));
        }
    }
    
    /**
     * Checks if all classes in objects exist
     * @return unknown
     */
    public function check_ObjectExistance(): Descriptor 
    {
        $tables = DB::table('objects')->distinct('classname')->get();
        $bad_classes = '';
        foreach ($tables as $table) {
            if (!Classes::searchClass($table->classname)) {
                $bad_classes .= (empty($bad_classes)?'':', ').$table->classname;
            }
        }
        if (empty($bad_classes)) {
            return $this->createResult(__('OK'),__('Check for non existance classes in objects'));            
        } else {
            return $this->createResult(__('FAILED'),__('Check for non existance classes in objects'),__("Classes ':bad_classes' dont exist.",['bad_classes'=>$bad_classes]));            
        }
    }
    
    public function check_ClassTableGaps(): Descriptor 
    {
        $table_tree = $this->getTableTree();
        $return = '';
        
        foreach ($table_tree as $master=>$table) {
            if ($result = $this->testTable($master,$table)) {
                $return .= (empty($return)?'':',').$result;        
            }
        }
        
        if (empty($return)) {
            return $this->createResult(__('OK'),__('Check for gaps in object tables'));
        } else {
            return $this->createResult(__('FAILED'),__('Check for gaps in object tables'),__("Objects ':return' have gaps.",['return'=>$return]));
        }
    }
    
    private function tableExists(string $table): bool 
    {
        $query = array_map('reset',DB::select('show tables'));    
        return in_array($table,$query);
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
