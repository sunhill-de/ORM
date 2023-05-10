<?php
/**
 * @file CalcClass.php
 * Provides the test object CalcClass that only has a calculated property
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\PropertyList;

/**
 * Only for testing purposes
 * @author klaus
 */
class CalcClass extends ORMObject {
	
    public $return = 'ABC';
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('dummyint');
        $list->calculated('calcfield');
        $list->calculated('calcfield2');
    }
    
    public function calculate_calcfield() {
        return $this->return;
    }
    
    public function calculate_calcfield2() {
        return $this->return."2";
    }
    
    public function set_return($value) {
        $this->return = $value;
        $this->recalculate();
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'calcclass');
        static::addInfo('table', 'calcclasses');
        static::addInfo('name_s', 'calcclass');
        static::addInfo('name_p', 'calcclassed');
        static::addInfo('description', 'A testing class');
        static::addInfo('options', 0);
    }
    
}

