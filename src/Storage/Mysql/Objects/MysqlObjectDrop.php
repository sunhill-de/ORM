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
        $query = DB::table('attributes')->get();
        foreach ($query as $attribute) {
            DB::table('attr_'.$attribute->name)->whereIn('id', DB::table($this->collection::getInfo('table'))->get())->delete();
        }
        DB::table('attributeobjectassigns')->whereIn('object_id', DB::table($this->collection::getInfo('table'))->get())->delete();
    }
    
    public function run()
    {
        $this->deleteAttributes();
        parent::run();
    }
    
    public function handlePropertyTags($property)
    {
        DB::table('tagobjectassigns')->whereIn('container_id', DB::table($this->collection::getInfo('table'))->get())->delete();
    }
    
}