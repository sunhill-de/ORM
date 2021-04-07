<?php

namespace Sunhill\ORM\Properties;

class oo_property_attribute_text extends oo_property_attribute {
	
    protected $type = 'attribute_text';

    protected function extract_value(\Sunhill\ORM\Storage\storage_base $loader) {
        return $this->value = $loader->entities['attributes'][$this->attribute_name]['textvalue'];
    }

    protected function insert_value(\Sunhill\ORM\Storage\storage_base $storage) {
        $storage->entities['attributes'][$this->attribute_name]['value'] = '';
        $storage->entities['attributes'][$this->attribute_name]['textvalue'] = $this->value;
    }
    
    
}