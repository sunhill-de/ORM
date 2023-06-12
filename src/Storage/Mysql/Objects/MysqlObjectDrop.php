<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Storage\Mysql\Utils\ClassTables;

class MysqlObjectDrop
{
    
    use ClassTables;
    
    public function __construct(public $storage) {}

    protected $id = 0;
    
    public function doDrop()
    {
        $this->additional_tables = $this->collectAdditionalTables();
        $this->deleteParentDatasets();
        $this->deleteTags();
        $this->deleteAttributes();
        
        $this->dropChildren();
    }

    protected function dropChildren()
    {
        $calling_class = $this->storage->getCaller()::getInfo('name');
        $children = Classes::getChildrenOfClass($calling_class);
        $children[$calling_class] = [];
        foreach ($children as $child => $info) {
            $this->dropClass($child);
        }        
    }
    
    protected function dropClass(string $child)
    {
        $this->deleteHelpingTables($child);
        $this->deleteCoreTable($child);
        $this->deleteObjects($child);
    }

    protected function deleteParentDatasets()
    {
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy);   // remove object
        array_shift($hirarchy); // remove self
        $classname = $this->storage->getCaller()::getInfo('name');
        
        foreach ($hirarchy as $parent) {
            $table = Classes::getTableOfClass($parent);
            DB::table($table)->whereIn('id',function ($query) use ($classname) {
               $query->select('id')->from('objects')->where('classname',$classname); 
            })->delete();
        }
    }
    
    protected function deleteTags()
    {
        $classname = $this->storage->getCaller()::getInfo('name');
        
        DB::table('tagobjectassigns')->whereIn('container_id',function($query) use ($classname) {
            $query->select('id')->from('objects')->where('classname',$classname);            
        })->delete();    
    }
    
    protected function deleteAttributes()
    {
        $classname = $this->storage->getCaller()::getInfo('name');
        $tables = DB::table('attributeobjectassigns')
            ->join('attributes','attributeobjectassigns.attribute_id','=','attributes.id')
            ->whereIn('attributeobjectassigns.object_id',function($query) use ($classname) {
                $query->select('id')->from('objects')->where('classname',$classname);
            })
            ->groupBy('attributes.id')
            ->select('attributes.name as name')->get();
        foreach ($tables as $table) {
            DB::table('attr_'.$table->name)->whereIn('object_id',function($query) use ($classname) {
                $query->select('id')->from('objects')->where('classname',$classname);
            })->delete();
        }
        
        DB::table('attributeobjectassigns')->whereIn('object_id',function($query) use ($classname) {
            $query->select('id')->from('objects')->where('classname',$classname);
        })->delete();
        
    }
    
    protected function deleteObjects($child)
    {
        DB::table('objects')->where('classname',$child)->delete();
    }
    
    protected function deleteHelpingTables($child)
    {
        $class_tables = $this->collectClassTables($child);
        foreach ($class_tables as $table) {
            Schema::drop($table);
        }
    }
    
    protected function deleteCoreTable($child)
    {
        Schema::drop(Classes::getTableOfClass($child));
    }
    
}