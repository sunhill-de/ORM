<?php
/**
 * @file SecondLevelChild.php
 * Provides the test object SecondLevelChild that is derrived from ReferenceOnly
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\PropertyList;

class SecondLevelChild extends ReferenceOnly {
    
    protected static function setupProperties(PropertyList $list)
    {
		$list->integer('childint');
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'secondlevelchild');
	    static::addInfo('table', 'secondlevelchildren');
	    static::addInfo('name_s', 'second level child');
	    static::addInfo('name_p', 'second level children');
	    static::addInfo('description', 'Another test class. A derrived class');
	    static::addInfo('options', 0);
	}
	
}
