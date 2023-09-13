<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\Utils\IgnoreSimple;
use Sunhill\ORM\Storage\StorageAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Facades\Collections;

/**
 * Helper class to load an object out of the database
 * @author klaus
 *
 */
class MysqlCollectionLoad extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers;
    
    protected $data;
    
    protected function getSimpleData($list)
    {
        $first = array_shift($list)::getInfo('table');
        $query = DB::table($first);
        foreach ($list as $class) {
            $class_table = $class::getInfo('table');
            $query->join($class_table,$first.'.id','=',$class_table.'.id');
        }
        $this->data = $query->where($first.'.id',$this->collection->getID())->first();        
    }
    
    public function run()
    {
        $list = $this->collectClasses();
        $this->getSimpleData($list);
        $this->runProperties();
    }
  
    protected function loadObject($id)
    {
        if (empty($id)) {
            return;
        }
        return Objects::load($id);
    }
    
    protected function loadCollection($collection, $id)
    {
        if (empty($id)) {
            return;
        }
        return Collections::loadCollection($collection, $id);        
    }

    protected function handleArrayOrMap($property)
    {
        $query = DB::table($this->getSpecialTableName($property))->where('id',$this->collection->getID())->get();
        $name = $property->name;
        foreach ($query as $entry) {
            switch ($property->element_type) {
                case PropertyObject::class:
                    $this->collection->getProperty($name)->loadIndexedValue($entry->index, $this->loadObject($entry->value));
                    break;
                case PropertyCollection::class:
                    $this->collection->getProperty($name)->loadIndexedValue($entry->index, $this->loadCollection($property->allowed_collection,$entry->value));
                    break;
                default:
                    $this->collection->getProperty($name)->loadIndexedValue($entry->index, $entry->value);
            }
        }        
    }
    
    public function handlePropertyArray($property)
    {
        $this->handleArrayOrMap($property);
    }
    
    public function handlePropertyBoolean($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyCalculated($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyCollection($property)
    {
        $name = $property->name;
        $object = $this->loadCollection($property->allowed_collection, $this->data->$name);
        $this->setValue($property, $object);        
    }
    
    public function handlePropertyDate($property)
    {        
        $this->loadValue($property);        
    }
    
    public function handlePropertyDateTime($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyEnum($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyFloat($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyInformation($property)
    {
        
    }
    
    public function handlePropertyInteger($property)
    {
         $this->loadValue($property);   
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($property)
    {
        $this->handleArrayOrMap($property);        
    }
    
    public function handlePropertyObject($property)
    {
        $name = $property->name;
        $object = $this->loadObject($this->data->$name);
        $this->setValue($property, $object);
    }
    
    public function handlePropertyTags($property)
    {
        
    }
    
    public function handlePropertyText($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyTime($property)
    {
        $this->loadValue($property);        
    }
    
    public function handlePropertyVarchar($property)
    {
        $this->loadValue($property);        
    }
    
}