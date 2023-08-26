<?php

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

class MysqlObjectPromote extends MysqlAction
{
    
    use ClassTables;
    
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
    
    protected function fillTableOf(string $target)
    {
        $properties = Classes::getNamespaceOfClass($target)::getPropertyDefinition();
        $data = ['id'=>$this->collection->getID()];
        foreach ($properties as $property) {
            if (isset($this->additional2[$property->getName()])) {
                $value = $this->additional2[$property->getName()];
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
                    case PropertyInformation::class:
                        $data[$property->getName()] = $value;
                        break;
                    case PropertyObject::class:
                    case PropertyCollection::class:
                        if (is_a($value,PropertiesCollection::class)) {
                            $data[$property->getName()] = $value->getID();
                        } else if (is_int($this->additional2[$property->getName])) {
                            $data[$property->getName()] = $value;                        
                        }
                        break;
                }
            }
        }
        DB::table(Classes::getTableOfClass($target))->insert($data);
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
        $this->fillDescendantTables();
        $this->changeObjects();
    }
    
}