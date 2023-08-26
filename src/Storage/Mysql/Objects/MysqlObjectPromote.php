<?php
/**
 * @file MyswmObjectPromote.php
 * The storage action that handles the promotion of an object
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-08-26
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: 88.3% (2023-08-26)
 * PSR-State: some type hints missing
 * Tests: tests/Unit/Storage/Objects/PromoteTest, tests/Feature/Objects/Objects/ObjectPromoteTest.php
 */

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Storage\Mysql\Utils\ClassTables;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Storage\Exceptions\ClassIsSameException;
use Sunhill\ORM\Storage\Exceptions\ClassNotARelativeException;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Properties\PropertyInformation;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyTags;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyKeyfield;
use Sunhill\ORM\Properties\PropertyExternalReference;

class MysqlObjectPromote extends MysqlAction
{
    
    use ClassTables;
    
    protected $dummy_object;
    
    protected function checkForSame()
    {
        if ($this->additional == ($this->collection)::class) {
            throw new ClassIsSameException("Can't promote to the same class.");
        }        
    }
    
    protected function checkForNotRelative()
    {
        $inheritance = Classes::getInheritanceOfClass($this->additional);
        if (!in_array((($this->collection)::class)::getInfo('name'), $inheritance)) {
            throw new ClassNotARelativeException("The target class is not an descendant of the original class.");
        }        
    }
    
    protected function fillDummyObject()
    {
        $this->dummy_object = new ($this->additional)();
        foreach ($this->collection::getAllPropertyDefinitions() as $property) {
            $name = $property->getName();
            switch ($property::class) {
                case PropertyArray::class:
                case PropertyMap::class:
                    foreach ($this->collection->$name as $key => $value) {
                        $this->dummy_object->$name[$key] = $value;
                    }
                    break;
                case PropertyCalculated::class:
                case PropertyTags::class:
                case PropertyInformation::class:
                case PropertyKeyfield::class: 
                case PropertyExternalReference::class:    
                    break;
                default:
                    $this->dummy_object->$name = $this->collection->$name;
                    break;
            }
        }
        foreach ($this->additional2 as $key => $value) {
            $this->dummy_object->$key = $value;
        }
    }
    
    protected function fillTableOf(string $target)
    {
        $properties = Classes::getNamespaceOfClass($target)::getPropertyDefinition();
        $data = ['id'=>$this->collection->getID()];
        $object_reference = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            switch ($property::class) {
                    case PropertyInteger::class:
                    case PropertyBoolean::class:
                    case PropertyVarchar::class:
                    case PropertyDate::class:
                    case PropertyTime::class:
                    case PropertyDatetime::class:
                    case PropertyFloat::class:
                    case PropertyText::class:
                    case PropertyEnum::class:
                    case PropertyCalculated::class:    
                        $data[$name] = $this->dummy_object->$name;
                        break;
                    case PropertyObject::class:                        
                    case PropertyCollection::class:
                        $value = $this->dummy_object->$name;
                        if (is_a($value,PropertiesCollection::class)) {
                            $data[$name] = $value->getID();
                        } else if (is_int($value)) {
                            $data[$name] = $value;
                        }
                        $value = $this->dummy_object->$name;
                        $value = is_a($value, PropertiesCollection::class)?$value->getID():$value;
                        $data[$name] = $value;                        
                        if (!is_null($value) && ($property::class == PropertyObject::class)) {
                            $object_reference[] = ['container_id'=>$this->collection->getID(),'target_id'=>$value];                            
                        }
                        break;
                    case PropertyArray::class:
                    case PropertyMap::class:
                        $array_data = [];
                        foreach ($this->dummy_object->$name as $key => $value) {
                            if ($property->getElementType() == PropertyObject::class) {
                                $value = is_a($value,PropertiesCollection::class)?$value->getID():$value;
                                $array_data[] = ['id'=>$this->collection->getID(),'index'=>$key, 'value'=>$value];
                                $object_reference[] = ['container_id'=>$this->collection->getID(),'target_id'=>$value];
                            } else {
                                $array_data[] = ['id'=>$this->collection->getID(),'index'=>$key, 'value'=>$value];                                
                            }
                        }
                        if (!empty($array_data)) {
                            DB::table(Classes::getTableOfClass($target).'_'.$property->getName())->insert($array_data);
                        }
                        break;
                    case PropertyInformation::class:
                        break;
            }
        }
        DB::table(Classes::getTableOfClass($target))->insert($data);
        if (!empty($object_reference)) {
            DB::table('objectobjectassigns')->insert($object_reference);
        }
    }
    
    protected function fillDescendantTables()
    {
        $target = ($this->additional)::getInfo('name');
        do {
            $this->fillTableOf($target);
            $target = Classes::getParentOfClass($target);
        } while ($target <> ($this->collection::class::getInfo('name')));
    }
    
    protected function changeObjects()
    {
        DB::table('objects')->where('id',$this->collection->getID())->update(['classname'=>($this->additional)::getInfo('name')]);    
    }
    
    public function run()
    {
        $this->checkForSame();
        $this->checkForNotRelative();
        $this->fillDummyObject();
        $this->fillDescendantTables();
        $this->changeObjects();
    }
    
}