<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Illuminate\Support\Facades\Schema;

class MysqlCollectionDrop extends MysqlAction implements HandlesProperties
{
   
    use PropertyHelpers;
        
    public function run()
    {
        $this->deleteFromParentTables($this->collectClasses());
        $this->dropChildTables();
    }
    
    protected function dropCollection($class)
    {
        $properties = $class::getPropertyDefinition();
        foreach ($properties as $property) {
            $this->mapProperty($property);
        }            
        Schema::drop($class::getInfo('table'));    
        $children = Classes::getChildrenOfClass($class::getInfo('name'),1);
        foreach ($children as $child => $subs) {
            $this->dropCollection(Classes::getNamespaceOfClass($child));
        }
    }
    
    protected function dropChildTables()
    {
        $this->dropCollection($this->collection::class);
        
    }
    
    protected function deleteFromParent($parent, $target)
    {
        DB::table($parent::getInfo('table'))->whereIn('id', DB::table($target::getInfo('table'))->select('id'))->delete();    
    }
    
    protected function deleteFromParentTables($list)
    {
        $target = array_shift($list);
        foreach ($list as $parent) {
            $this->deleteFromParent($parent, $target);
        }
    }
    
    protected function handleArrayOrMap($property)
    {
        Schema::drop($this->getSpecialTableName($property));
    }
    
    public function handlePropertyArray($property)
    {
        $this->handleArrayOrMap($property);
    }
    
    public function handlePropertyBoolean($property)
    {
    }
    
    public function handlePropertyCalculated($property)
    {
    }
    
    public function handlePropertyCollection($property)
    {
    }
    
    public function handlePropertyDate($property)
    {
    }
    
    public function handlePropertyDateTime($property)
    {
    }
    
    public function handlePropertyEnum($property)
    {
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyFloat($property)
    {
    }
    
    public function handlePropertyInformation($property)
    {
        
    }
    
    public function handlePropertyInteger($property)
    {
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
    }
    
    public function handlePropertyTags($property)
    {        
    }
    
    public function handlePropertyText($property)
    {
    }
    
    public function handlePropertyTime($property)
    {
    }
    
    public function handlePropertyVarchar($property)
    {
    }        
}