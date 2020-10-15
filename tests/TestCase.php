<?php

namespace Sunhill\ORM\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    /**
     * Wrapper für Wertermittlung
     * Ist $fieldname nur ein einfacher string wird $loader->$fieldname zurückgegeben
     * Ist $fieldname in der Form irgendwas[index] wird $loader->$irgendwas[$index] zurückgegeben
     * Ist $fieldname in der Form irgendwas->subfeld wird $loader->$irgendwas->$subfeld zurückgegeben
     * Ist $fieldname in der Form irgendwas[index]->subfeld wird $loader->$irgendwas[$index]->$subfeld zurückgegeben
     * @param unknown $loader
     * @param unknown $fieldname
     * @return unknown
     */
    protected function get_field($loader,$fieldname) {
        $match = '';
        if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            $index = $match['index'];
            return $loader->$name[$index]->$subfield;
        } else if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]\[(?P<index2>\w+)\]/',$fieldname,$match)) {
            $name = $match['name'];
            $index2 = $match['index2'];
            $index = $match['index'];
            return $loader->$name[$index][$index2];
        } else if (preg_match('/(?P<name>\w+)->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            return $loader->$name->$subfield;
        } if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]/',$fieldname,$match)){
            $name = $match['name'];
            $index = $match['index'];
            return $loader->$name[$index];
        }  else if (is_string($fieldname)){
            return $loader->$fieldname;
        } else {
            return $loader;
        }
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
       // Classes::add_class_dir(dirname(__FILE__).'/../objects');        
    }
    
}
