<?php 
/**
 * @file StorageModuleMySQLStrings.php
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

use Illuminate\Support\Facades\DB;

class StorageModuleMySQLStrings extends StorageModuleBase 
{
    
    public function load(int $id) 
    {
        $references = DB::table('stringobjectassigns')->where('container_id','=',$id)->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if (!isset($this->storage->entities[$reference->field])) {
                $this->storage->entities[$reference->field] = [];
            }
            $this->storage->entities[$reference->field][$reference->index] = $reference->element_id;
        }
        return $id;
    }
    
    public function insert(int $id) 
    {
        $properties = $this->storage->filterStorage('strings');
        if (empty($properties)) {
            return $id;
        }
        $inserts = [];
        foreach($properties as $property => $values) {
            foreach($values as $index=>$value) {
                $inserts[] = ['container_id'=>$id,'element_id'=>$value,'field'=>$property,'index'=>$index];
            }
        }
        DB::table('stringobjectassigns')->insert($inserts);
        return $id;
    }
    
    public function update(int $id) 
    {
        $properties = $this->storage->filterStorage('strings');
        if (empty($properties)) {
            return $id;
        }
        $inserts = [];
        foreach ($properties as $property=>$diff) {
            if (!empty($diff['ADD'])) {
                foreach ($diff['ADD'] as $index=>$value) {
                    $inserts[] = ['container_id'=>$id,'element_id'=>$value,'field'=>$property,'index'=>$index];
                }
            }
            if (!empty($diff['DELETE'])) {
                DB::table('stringobjectassigns')->where('container_id','=',$id)
                                                ->whereIn('element_id',$diff['DELETE'])->delete();
            }
        }
        DB::table('stringobjectassigns')->insert($inserts);
        return $id;
    }
    
    public function delete(int $id) 
    {
        DB::table('stringobjectassigns')->where('container_id','=',$id)->delete(); 
        return $id;
    }
    
    /**
     * Löscht die höhergestellten Tabellen
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::degrade()
     */
    public function degrade(int $id,array $degration_info) 
    {
        $properties = $this->storage->filterStorage('strings');
        foreach ($properties as $property=>$payload) {
                DB::table('stringobjectassigns')
                ->where('container_id',$id)
                ->where('field',$property)->delete();
        }
        return $id;
    }
}
