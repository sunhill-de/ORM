<?php
/**
 * @file ScenarioWithObjects.php
 * An extension to scenarios that handle objects
 * Lang en
 * Reviewstatus: 2021-08-05
 * Localization: none required
 * Documentation: complete
 * Tests: tests/Unit/Scenarios/ScenariosWithObjectsTest.php
 * Coverage: unknown
 * Dependencies: Functioning object subsystem, class and object manager
 */

namespace Sunhill\Basic\Tests\Scenario;

use Sunhill\ORM\Facades\Classes;

trait ScenarioWithObjects {

    protected function SetUpObjects() {
        $objects = $this->GetObjects();
        foreach ($objects as $name => $description) {
            $this->SetUpObject($name,$description);
        }
    }
    
    protected function SetUpObject($name,$description) {
        list($fields,$values) = $description;
        $classname = Classes::get_namespace_of_class($name);
        foreach ($values as $key => $value) {
            $class = new $classname();            
            for ($i=0;$i<count($fields);$i++) {
                $field_name = $fields[$i];
                $field_value = $value[$i];
                if ($field_name == 'tags') {
                    $this->handleTags($class,$field_value);
                } else if (is_string($field_value) && substr($field_value,0,2) == '=>') {
                    $this->handleReference($class,$)
                } else if (is_array($field_value)) {
                    
                } else {
                    $class->$field_name = $field_value;                    
                }
            }
            $class->commit();
        }
    }
    
    abstract function GetObjects();
}
