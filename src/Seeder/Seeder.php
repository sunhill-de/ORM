<?php
/**
 * @file Seeder.php
 * Defines the Seeder class, a base class for object seeds
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-05-21
 * Localization: in progress
 * Documentation: complete
 * Tests: Unit/PropertyTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Seeder;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\DB;

abstract class Seeder {
    
    protected $key_objects = [];
    
    abstract public function Seed();
    
    protected function SeedObject(string $class,array $fields,array $values) {
        $object_name = Classes::getNamespaceOfClass($class);
        foreach ($values as $key => $value_array) {
            $object = new $object_name();
            $i=0;
            foreach ($fields as $field) {
                $value = $value_array[$i];
                if (!is_null($value)) {
                    if ($field == 'tags') {
                        foreach ($value as $tag) {
                            $object->tags->stick($tag);
                        }
                    } else if (is_array($value)) {
                        foreach ($value as $subvalue) {
                            $object->$field[] = $this->SolveValue($subvalue);
                        }
                    } else {
                        $object->$field = $this->SolveValue($value);
                    }
                }
                $i++;
            }
            $object->commit();
            if (is_string($key)) {
                $this->key_objects[$key] = $object;
            }
        }        
    }
    
    private function SolveValue($value) {
        if (is_string($value)) {
            if ((substr($value,0,2) == '->') || (substr($value,0,2) == '=>')) {
                $value = $this->getkeyObject(substr($value,2));
            } else if ($value == 'null') {
                $value = null;
            }
        } 
        return $value;
    }
    
    public function GetKeyObject(string $key) {
        if (isset($this->key_objects[$key])) {
            return $this->key_objects[$key];
        } else {
            return null;
        }
    }
}