<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Storage\Mysql\Utils\ClassTables;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionDrop;

class MysqlObjectDrop extends MysqlCollectionDrop
{
    
    protected function deleteAttributes()
    {
        $table = ($this->collection)::getInfo('table');
        $query = DB::table('attributes')->get();
        foreach ($query as $attribute) {
            DB::table('attr_'.$attribute->name)->whereIn('object_id', DB::table($table)->select('id'))->delete();
        }
        DB::table('attributeobjectassigns')->whereIn('object_id', DB::table($table)->select('id'))->delete();
    }
    
    public function run()
    {
        $this->deleteAttributes();
        $this->deleteTags();
        parent::run();
    }
    
    protected function deleteTags()
    {
        DB::table('tagobjectassigns')->whereIn('container_id', DB::table(($this->collection)::getInfo('table'))->select('id'))->delete();        
    }
    
    public function handlePropertyTags($property)
    {
    }
    
}