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
class TagChecks extends ChecksBase 
{
    
    /**
     * Checks if all tags have existing or no parents at all
     * @return unknown
     */
    public function check_TagsWithNotExistingParents(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tags','parent_id','tags','id',true)) {
            if (!$repair) {
                $this->fail(__("Parents of tags ':entries' dont exist.",['entries'=>$entries]));
            } else {
                
            }
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all entries in the tagcache have an existing tag
     * @return unknown
     */
    public function check_TagCacheWithNotExistingTags(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tagcache','tag_id','tags','id')) {
            if (!$repair) {
              $this->fail(__("Tags ':entries' dont exist.",['entries'=>$entries]));
            } else {
                $this->unrepairable(__("Tags ':entries' dont exist.",['entries'=>$entries]));                
            }
        } else {
            $this->pass();
        }
    }
    
    private function getTag($tags,int $id)
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
    public function check_TagCacheConsistency($repair)
    {
        $tags = DB::table('tags')->get();
        $result = [];
        $this->buildCache($result,$tags);
        $count = DB::table('tagcache')->count();
        if ($count !== count($result)) {
            if ($repair) {
                $this->unrepairable(__("Entry count :count doenst match expected :expect",['count'=>$count,'expect'=>count($result)]));
            } else {
                $this->fail(__("Entry count :count doenst match expected :expect",['count'=>$count,'expect'=>count($result)]));
            }
        }
        $tagcache_entries = DB::table('tagcache')->get();
        $entries = '';
        foreach ($tagcache_entries as $entry) {
            if (!in_array($entry->name,$result)) {
                $entries .= (empty($entries)?$entry->name:','.$entry->name);
            }
        }
        if (empty($entries)) {
            $this->pass();
        } else {
            if ($repair) {
                $this->unrepairable(__("Entries :entries don't match.",['entries'=>$entries]));
            } else {
                $this->fail(__("Entries :entries don't match.",['entries'=>$entries]));
            }
        }
    }
    
    /**
     * Checks if all tags in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_TagObjectAssignsTagsExist(bool $repar)
    {
        if ($entries = $this->checkForDanglingPointers('tagobjectassigns','tag_id','tags','id',true)) {
            $this->fail(__("Tags ':entries' dont exist.",['entries'=>$entries]));
        } else {
            $this->pass();
        }
    }
    
    /**
     * Checks if all objects in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_TagObjectAssignsObjectsExist(bool $repair)
    {
        if ($entries = $this->checkForDanglingPointers('tagobjectassigns','container_id','objects','id',true)) {
            $this->fail(__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            $this->pass();
        }
    }
            
}
