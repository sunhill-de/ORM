<?php

/**
 * @file PropertyQuery.php
 * Provides an convenient method to receive a list of properties
 * Lang en
 * Reviewstatus: 2022-06-22
 * Localization: incomplete
 * Documentation: incomplete
 * Tests: Unit/Objects/PropertyQueryTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\PropertyQuery;

use Sunhill\ORM\Query\ArrayQuery;
use Sunhill\ORM\Properties\PropertyExternalReference;

class PropertyQuery extends ArrayQuery
{
    
    protected $current;
  
    protected $include_values = false;
    
    /**
     * Due the fact, that the constructor is called by the object, it gets the property list of that object and performs all
     * further processings on this copy
     * @param $properties array of Property: The list of properties of that object
     */
    public function __construct($properties, $include_values = false)
    {
        parent::__construct();
        $this->current = $properties;
        $this->include_values = $include_values;
    }  

    protected function getRawData()
    {
        $result = [];
        foreach ($this->current as $key => $value) {
            $entry = new \StdClass();
            $entry->name = $key;
            $entry->owner = $value->getOwner();
            $entry->type = $value::class;
            $entry->dirty = $value->getDirty();
            $entry->readonly = $value->getReadonly();
            $entry->unit = $value->getUnit();
            $entry->semantic = $value->getSemantic();
            $entry->searchable = $value->getSearchable();
            if ($value->getInitialized() && $this->include_values && !is_a($value, PropertyExternalReference::class)) {
                $entry->value = $value->getValue();
                $entry->shadow = $value->getShadow();
            } 
            $result[] = $entry;
        }
        return $result;
    }
    
}
