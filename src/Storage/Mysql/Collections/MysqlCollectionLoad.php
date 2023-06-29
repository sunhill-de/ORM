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
        $first = array_shift($list);
        $query = DB::table($first::getInfo('table'));
        foreach ($list as $class) {
            $query->join($class::getInfo('table'),'id','=','id');
        }
        $this->data = $query->where('id',$this->collection->getID())->first();        
    }
    
    public function run()
    {
        $list = $this->collectClasses();
        $this->getSimpleData($list);
        $this->runProperties();
    }
  
    protected function handleArrayOrMap($property)
    {
        $query = DB::table($this->getSpecialTableName($property))->where('id',$this->collection->getID())->get();
        $name = $property->name;
        foreach ($query as $entry) {
            switch ($property->element_type) {
                case PropertyObject::class:
                    $this->collection->$name[$entry->index] = Objects::load($entry->value);
                    break;
                case PropertyCollection::class:
                    $this->collection->$name[$entry->index] = Collections::loadCollection($entry->value);
                    break;
                default:
                    $this->collection->$name[$entry->index] = $entry->value;                    
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
        $id = $this->data->$name;
        $this->setValue($property, Objects::load($id));
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