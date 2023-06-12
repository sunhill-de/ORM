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

    public function handlePropertyBoolean($property)
    {
        
    }
    
    public function handlePropertyCalculated($property)
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
    
    public function handlePropertyFloat($property)
    {
        
    }
    
    public function handlePropertyInteger($property)
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
    
    public function handlePropertyTimestamp($property)
    {
        
    }
    
    public function handlePropertyVarchar($property)
    {
        
    }
    
}