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

use Illuminate\Support\Facades\DB;

/**
 * Provides checks for the checking subsystem of sunhill for the orm system
 * @author klaus
 *
 */
class TagChecks extends ChecksBase 
{
    
    /**
     * Checks if all tags have existing or no parents at all
     * @return unknown
     * Test: testRepairableProblems
     */
    public function check_TagsWithNotExistingParents(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tags','parent_id','tags','id',false)) {
            if (!$repair) {
                $this->fail(__(":entries tags with no parents.",['entries'=>$entries]));
            } else {
                $entries = $this->repairDanglingPointers('tags','parent_id','tags','id');
                $this->repair(__("Removed :entries tags with no parents.",['entries'=>$entries]));
            }
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all entries in the tagcache have an existing tag
     * @return unknown
     * Test: testRepairableProblems
     */
    public function check_TagCacheWithNotExistingTags(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tagcache','tag_id','tags','id')) {
            if (!$repair) {
               $this->fail(__("There are :entries entries in the tagcache that doesn't have a tag.",['entries'=>$entries]));
            } else {
               $entries = $this->repairDanglingPointers('tagcache', 'tag_id', 'tags', 'id');
               $this->repair(__("Removed :entries entries from tagcache that doesn't have a tag.",['entries'=>$entries]));
            }
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all tags in the tagobjectassigns table exists
     * @return unknown
     * Test: testRepairableProblems
     */
    public function check_TagObjectAssignsTagsExist(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tagobjectassigns','tag_id','tags','id',true)) {
            if ($repair) {
                $entries = $this->repairDanglingPointers('tagobjectassigns','tag_id','tags','id');
                $this->repair("Removed :entries entries in the tagobjectassigns table that have no corresponding tag.",['entries'=>$entries]);
            } else {
                $this->fail(":entries entries in the tagobjectassigns table have no corresponding tag.",['entries'=>$entries]);
            }
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all objects in the tagobjectassigns table exists
     * @return unknown
     * Test: testRepairableProblems
     */
    public function check_TagObjectAssignsObjectsExist(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tagobjectassigns','container_id','objects','id',true)) {
            if ($repair) {
                $entries = $this->repairDanglingPointers('tagobjectassigns','container_id','objects','id');
                $this->repair(__("Removed :entries entries in the tagobjectassigns table doesn't have a corresponding object.",array('entries'=>$entries)));
            } else {
                $this->fail(__(":entries entries in the tagobjectassigns table doesn't have a corresponding object.",array('entries'=>$entries)));                
            }
        } else {
            $this->pass();
        }
    }
    
    /**
     * Loads all tags in an array
     * @return \Illuminate\Support\Collection[]
     * Test: testBuildTagMatrix
     */
    protected function buildTagMatrix()
    {
        $result = [];
        $query = DB::table('tags')->get();
        foreach ($query as $tag) {
            $result[$tag->id] = $tag;
        }
        return $result;
    }
        
    /**
     * Builds the fullpath of the given tag
     * @param int $id
     * @param array $matrix
     * @return unknown|string
     * Test: testGetFullpath
     */
    protected function getFullpath(int $id, array $matrix)
    {
        if ($id) {
            if (empty($parent = $this->getFullpath($matrix[$id]->parent_id, $matrix))) {
                return $matrix[$id]->name;                
            } else {
                return $parent.".".$matrix[$id]->name;
            }
        } else {
            return "";
        }
    }
    
    /**
     * Adds to $result all possible permutations of the tag $id in the matrix $matrix (and adds the $postfix)
     * @param unknown $result
     * @param unknown $matrix
     * @param unknown $id
     * @param string $postfix
     * Test: testAddPermutations
     */
    protected function addPermutations(&$result, $matrix, $id, $original_id, $postfix="")
    {
        if ($id) {
            $newpostfix = $matrix[$id]->name.(empty($postfix)?"":".".$postfix);
            $entry = new \StdClass();
            $entry->id = $original_id;
            $entry->path_name = $newpostfix;
            $result[] = $entry;
            $this->addPermutations($result, $matrix, $matrix[$id]->parent_id, $original_id, $newpostfix);
        }
    }
    
    protected function buildExpectedTagCache($matrix)
    {
        $result = [];
        foreach ($matrix as $id => $entry) {
            $this->addPermutations($result, $matrix, $id, $id);
        }
        return $result;
    }
    
    public function check_ExpectedTagcacheEntries(bool $repair)
    {
        $missing = [];
        $expected_list = $this->buildExpectedTagCache($this->buildTagMatrix());
        foreach ($expected_list as $entry) {
            $query = DB::table('tagcache')->where('path_name',$entry->path_name)->get();
            if (!count($query)) {
                $missing[] = $entry;
            } 
        }
        if (count($missing)) {
            if ($repair) {
                foreach ($missing as $entry) {
                    DB::table('tagcache')->insert(['path_name'=>$entry->path_name,'tag_id'=>$entry->id]);
                    $this->repair(":entries entries missing in the tagcache where added.",["entries"=>count($missing)]);
                }
            } else {
                $this->fail(__(":entries entries are missing in the tagcache.",["entries"=>count($missing)]));
            }
        } else {
            $this->pass();
        }
       
    }
            
    protected function hasEntry(array $expect, int $id, string $name): bool
    {
        foreach ($expect as $entry) {
            if (($entry->path_name == $name) && ($entry->id == $id)) {
                return true;
            }
        }
        return false;
    }
    
    public function check_UnexpectedTagcacheEntries(bool $repair)
    {
        $missing = [];
        $expected_list = $this->buildExpectedTagCache($this->buildTagMatrix());
        $query = DB::table('tagcache')->get();
        foreach ($query as $entry) {
            if (!$this->hasEntry($expected_list, $entry->tag_id, $entry->path_name)) {
                $missing[] = $entry;
            }
        }
        if (count($missing)) {
            if ($repair) {
                foreach ($missing as $entry) {
                    DB::table('tagcache')->where('tag_id',$entry->tag_id)->where('path_name',$entry->path_name)->delete();
                    $this->repair(":entries entries where too m in the tagcache where added.",["entries"=>count($missing)]);
                }
            } else {
                $this->fail(__(":entries entries are missing in the tagcache.",["entries"=>count($missing)]));
            }
        } else {
            $this->pass();
        }
        
    }
    
}
