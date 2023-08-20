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

class MysqlObjectDegrade extends MysqlAction
{
    
    use ClassTables;
    
    protected function checkForSame()
    {
        if ($this->additional == ($this->collection)::class) {
            throw new ClassIsSameException("Can't degrade to the same class.");
        }        
    }
    
    protected function checkForNotRelative()
    {
        $inheritance = Classes::getInheritanceOfClass($this->collection);
        array_pop($inheritance); // Can't degrade to ORMObject
        if (!in_array(($this->additional)::getInfo('name'), $inheritance)) {
            throw new ClassNotARelativeException("The target class is not an ancestor of the original class.");
        }        
    }
    
    protected function removeOldSecondaryTables($current)
    {
        $properties = $this->collection->propertyQuery()->get();
        foreach ($properties as $property) {
            if (($property->owner == $current) && (($property->type == PropertyArray::class) || ($property->type == PropertyMap::class))) {
                DB::table($current::getInfo('table').'_'.$property->name)->where('id',$this->collection->getID())->delete();                
            }
        }        
    }
    
    protected function removeOldDescendands()
    {
        $current = ($this->collection::class);
        do {
            DB::table($current::getInfo('table'))->where('id',$this->collection->getID())->delete();
            $this->removeOldSecondaryTables($current);
            $current = get_parent_class($current);
        } while ($current <> $this->additional);
    }
    
    protected function changeObjects()
    {
        DB::table('objects')->where('id',$this->collection->getID())->update(['classname'=>($this->additional)::getInfo('name')]);    
    }
    
    public function run()
    {
        $this->checkForSame();
        $this->checkForNotRelative();
        $this->removeOldDescendands();
        $this->changeObjects();
    }
    
}