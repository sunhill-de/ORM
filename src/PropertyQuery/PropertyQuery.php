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
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyInformation;
use Sunhill\ORM\Properties\PropertyKeyfield;
use Sunhill\ORM\Properties\Utils\DefaultNull;

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
            if ($entry->dynamic = $value->getActualPropertiesCollection()->isDynamicProperty($key)) {
                $entry->attribute_id = $value->getAttributeID();   
            }
            switch ($value::class) {
                case PropertyArray::class:
                case PropertyMap::class:
                    $entry->element_type = $value->getElementType();
                    if ($value->getElementType() == PropertyObject::class) {
                        $entry->allowed_classes = $value->getAllowedClasses();
                    }
                    if ($value->getElementType() == PropertyCollection::class) {
                        $entry->allowed_collection = $value->getAllowedCollection();
                    }
                    break;
                case PropertyObject::class:    
                    $entry->allowed_classes = $value->getAllowedClasses();
                    break;
                case PropertyCollection::class:
                    $entry->allowed_collection = $value->getAllowedCollection();
                    break;
                case PropertyEnum::class:
                    $entry->enum_values = $value->getEnumValues();
                    break;
                case PropertyVarchar::class:
                    $entry->max_len = $value->getMaxLen();
                    break;
                case PropertyInformation::class:
                    $entry->path = $value->getPath();
                    break;
                case PropertyKeyfield::class:
                    $entry->build_rule = $value->getBuildRule();
                    break;
            }
            $entry->dirty = $value->getDirty();
            $entry->readonly = $value->getReadonly();
            $entry->unit = $value->getUnit();
            $entry->semantic = $value->getSemantic();
            $entry->searchable = $value->getSearchable();
    
            if ($value->getInitialized() && $this->include_values && !is_a($value, PropertyExternalReference::class) && ($value::class !== PropertyInformation::class)) {
                $entry->value = $value->getValue();
                $entry->shadow = $value->getShadow();
            } else {
                $default = $value->getDefault();
                if ($default == DefaultNull::class) {
                    $entry->value = null;
                    $entry->shadow = null;
                } else if (isset($default)) {
                    $entry->value = $default;
                    $entry->shadow = $default;
                }
            }
            $result[] = $entry;
        }
        return $result;
    }
    
}
