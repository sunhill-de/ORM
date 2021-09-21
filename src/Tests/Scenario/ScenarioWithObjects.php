<?php
/**
 * @file ScenarioWithObjects.php
 * An extension to scenarios that handle objects
 * Lang en
 * Reviewstatus: 2021-08-05
 * Localization: none required
 * Documentation: complete
 * Tests: tests/Unit/Scenarios/ScenariosWithObjectsTest.php, tests/Feature/Scenarios/ScenarioWithObjectsTest.php
 * Coverage: unknown
 * Dependencies: Functioning object subsystem, class and object manager
 */

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\ORM\Facades\Classes;
use Sunhill\Basic\SunhillException;
use Sunhill\ORM\Objects\oo_object;
use Illuminate\Support\Facades\DB;

trait ScenarioWithObjects {

    protected $references = []; /**<< Storage for the references */
    
    /**
     * This method is called by the scenario to setup the single objects
     * The abstract method GetObjects returns an associative array, where the key is the name of the object
     * and the value is an array of two elements, the first element in turn is an array with the name of the fields
     * the second element is an array of arrays with the values of these fields
     * Example:
     *   class1 => [[element1,element2,element3],
     *              [
     *                  [element1_value1,element2_value1,element3_value1],
     *                  'key2'=>[element1_value2,element2_value2,element3_value2],
     *                  ...
     *              ]
     *             ],
     *  class2 => ...
     */
    protected function SetUpObjects() {
       // Classes::flushClasses();
        $this->clearTables();
        $this->references = [];
        $objects = $this->GetObjects();
        foreach ($objects as $name => $description) {
            $this->handleClass($name,$description);
        }
    }
    
    protected function gatherTables() {
        $result = ['objectobjectassigns','stringobjectassigns','tagobjectassigns','caching'];
    
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if (is_a($class,oo_object::class,true)) {
                if (!in_array($class::$object_infos['table'],$result)) {
                    $result[] = $class::$object_infos['table'];
                }
            }
        }
        
        return $result;
    }
    
    protected function clearTables() {
        $tables = $this->gatherTables();
        foreach ($tables as $table) {
            DB::statement('truncate '.$table);
        }
    }
    
    /**
     * For each row in the descriptor array this method is called with the name of the class and the descriptor of this class
     * The descriptor is the two element array described above
     * @param $name string The name of the class
     * @param $description array The descriptor of this class (an two element array)
     */
    protected function handleClass(string $name,array $descriptor) {
        if (count($descriptor) !== 2) {
            throw new SunhillException("Invalid object descriptor: Elementcount is not 2");
            return;
        }
        list($fields,$values) = $descriptor;
        
        // Traverse through the values array and create an object for each row
        foreach ($values as $reference=>$single_values) {
            $this->handleObject($name,$reference,$fields,$single_values);
        }
    }
    
    /**
     * This method is called for each object that has to be created (each row in the values array)
     * @param $name string The name of the class
     * @param $reference string|int If this is a string, than the object should be stored as an reference
     * @param $fields array of strings: The name of the fields
     * @param $values array of void: The value of the fields of this objects
     * @throws SunhillException If the number of values is not equal to the number of fields
     */
    protected function handleObject(string $name,$reference,array $fields,array $values) {
        if (count($fields) !== count($values)) {
            throw new SunhillException("Invalid object descriptor: The count of values has to be the same as the count of fields");
            return;
        }
        
        // Get name class name with namespace and create an instance
        $classname = $this->getNamespace($name);
        Classes::registerClass($classname);
        $class = new $classname();
        $this->handleFields($class,$fields,$values);
        $class->commit();
        
        if (is_string($reference)) {
            $this->storeReference($reference,$class);
        }
    }
    
    /**
     * We can't depend on classmanager so implement this routine by hand
     * @param unknown $test
     */
    private function getNamespace($test) {
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if (is_a($class,oo_object::class,true)) {
                if ($class::$object_infos['name'] == $test) {
                    return $class;
                }
            }
        }
    }
    
    protected function handleFields($class,$fields,$values) {
        // Traverse both array at the same time, therefore an for loop
        for ($i=0;$i<count($fields);$i++) {
                $field_name = $fields[$i];
                $field_value = $values[$i];
                if ($field_name == 'tags') {
                    $this->handleTags($class,$field_value);
                } else if (is_string($field_value) && substr($field_value,0,2) == '=>') {
                    $this->handleReference($class,$field_name,$field_value);
                } else if (is_array($field_value)) {
                    $this->handleArray($class,$field_name,$field_value);
                } else {
                    $this->handleField($class,$field_name,$field_value);
                }
            }
    }
    
    protected function handleTags($class,$tags) {
        if (is_null($tags)) {
            return;
        }
        
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $class->tags->stick($tag);
            }
        } else {
            $class->tags->stick($tags);
        }
    }
    
    protected function handleReference($class,$field_name,$reference) {
        $class->$field_name = $this->getReference($reference);
    }
    
    protected function handleArray($class,string $field_name,array $values) {
        foreach ($values as $value) {
            if (is_string($value) && (substr($value,0,2) == '=>')) {
                $class->$field_name[] = $this->getReference($value);
            } else {
                $class->$field_name[] = $value;
            }
        }   
    }
    
    /**
     * Handles a simple field assigns
     */
    protected function handleField($class,string $field_name,$value) {
        if (is_null($value)) { // Ignore null fields
            return;
        }
        $class->$field_name = $value;
    }
    
    /**
     * Writes the reference in the array
     * @param $reference string: The name of the reference
     * @param $class oo_object: The object to store
     * @throws SunhillException if the reference is already in use
     */
    protected function storeReference(string $reference,$class) {
        if (isset($this->references[$reference])) {
            throw new SunhillException("The reference '$reference' is already in use.");
            return;
        }
        $this->references[$reference] = $class;
    }
    
    /**
     * Returns the reference from the array
     * @param $reference string: The name of the reference
     * @return oo_object: The stored object 
     * @throws SunhillException if the reference does not exist
     */
    protected function getReference(string $reference) {
        if (substr($reference,0,2) == '=>') { // Remove reference string
            $reference = substr($reference,2);
        }
        
        if (!isset($this->references[$reference])) {
            throw new SunhillException("The reference '$reference' doesn't exist.");
            return;
        }
        return $this->references[$reference];
    }
    
    abstract function GetObjects();
}
