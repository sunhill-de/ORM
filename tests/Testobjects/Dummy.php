<?php
/**
 * @file Dummy.php
 * Provides the test object Dummy that only has an integer as property
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
class Dummy extends ORMObject {
	
    protected static function setupProperties(PropertyList $list)
    {
		$list->integer('dummyint')->searchable();
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'dummy');
	    static::addInfo('table', 'dummies');
	    static::addInfo('name_s', 'dummy');
	    static::addInfo('name_p', 'dummies');
	    static::addInfo('description', 'A dummy test object class that only provides an integer');
	    static::addInfo('options', 0);
	    static::addInfo('special', true);
	}
	
}

