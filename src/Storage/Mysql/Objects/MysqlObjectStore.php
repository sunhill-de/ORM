<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionStore;
use Sunhill\ORM\Objects\ORMObject;

class MysqlObjectStore extends MysqlCollectionStore
{

    public function run()
    {
        $this->prerunTables();
        parent::run();
    }
    
    protected function getClassList($target)
    {
        $result = [];
        $target = $target::class;
        while ($target !== ORMObject::class) {
            $result[] = $target;
            $target = get_parent_class($target);
        }
        return $result;
    }
    
    protected function prerunTables()
    {
        $list = $this->getClassList($this->collection); 
        foreach ($list as $parent) {
            $this->tables[$parent::getInfo('table')] = [];
        }
    }
    
    protected function handleAttribute($property)
    {
        $this->addEntry('attr_'.$property->name,'value',$property->value);
        $this->addEntryRecord('attributeobjectassigns', ['attribute_id'=>$property->attribute_id]);
    }
    
    protected function storeMainTable()
    {
        $this->tables['objects']['classname'] = $this->collection::getInfo('name');
        $this->storeTable('objects', $this->tables['objects']);
        $this->id = DB::getPdo()->lastInsertId();
        $this->collection->setID($this->id);
        unset($this->tables['objects']);
    }
    
    public function handlePropertyInformation($property)
    {
    }
    
    public function handlePropertyTags($property)
    {
        foreach ($property->value as $tag) {
            $this->addEntryRecord('tagobjectassigns',['tag_id'=>$tag->getID()]);            
        }
    }
    
}