<?php
/**
 * @file MysqlMigrateFresh
 * Helper that creates a fresh table of the given object or collection
 */
namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;

class MysqlObjectMigrateFresh extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers;
    
    public function run()
    {
        $list = $this->collectClasses();
        $this->deleteClassTables($list);
        $this->runProperties();
    }
    
    protected function deleteClassTables($list)
    {
        
    }
    
    protected function handleArrayOrMap($property)
    {
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