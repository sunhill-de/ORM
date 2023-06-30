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
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;

class MysqlObjectPromote extends MysqlAction implements HandlesProperties
{
        
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