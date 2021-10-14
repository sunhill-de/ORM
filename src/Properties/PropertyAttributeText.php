<?php

namespace Sunhill\ORM\Properties;

class PropertyAttributeText extends PropertyAttribute 
{
	
    protected $type = 'attribute_text';

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