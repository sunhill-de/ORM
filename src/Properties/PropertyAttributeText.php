<?php

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;

class PropertyAttributeText extends PropertyAttribute 
{
	
    protected static $type = 'attribute_text';

    protected function extractValue(StorageBase $loader) 
    {
        return $this->value = $loader->entities['attributes'][$this->attribute_name]['textvalue'];
    }

    protected function insertValue(StorageBase $storage) 
    {
        $storage->entities['attributes'][$this->attribute_name]['value'] = '';
        $storage->entities['attributes'][$this->attribute_name]['textvalue'] = $this->value;
    }
    
    
}