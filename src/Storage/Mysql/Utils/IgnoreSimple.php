<?php

/**
 * Many Handler ignore "simple" properties even though the interface forces methods for them. This
 * trait makes it easier to ignore them. 
 */
namespace Sunhill\ORM\Storage\Mysql\Utils;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\Property;
use Illuminate\Support\Facades\Schema;

trait IgnoreSimple
{

    public function handlePropertyBoolean(Property $property)
    {
        
    }
    
    public function handlePropertyCalculated(Property $property)
    {
        
    }
    
    public function handlePropertyDate(Property $property)
    {
        
    }
    
    public function handlePropertyDateTime(Property $property)
    {
        
    }
    
    public function handlePropertyEnum(Property $property)
    {
        
    }
    
    public function handlePropertyFloat(Property $property)
    {
        
    }
    
    public function handlePropertyInteger(Property $property)
    {
        
    }
    
    public function handlePropertyTags(Property $property)
    {
        
    }
    
    public function handlePropertyText(Property $property)
    {
        
    }
    
    public function handlePropertyTime(Property $property)
    {
        
    }
    
    public function handlePropertyTimestamp(Property $property)
    {
        
    }
    
    public function handlePropertyVarchar(Property $property)
    {
        
    }
    
}