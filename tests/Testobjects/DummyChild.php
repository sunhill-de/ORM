<?php
/**
 * @file DummyChild.php
 * Provides the test object DummyChild that is derrived and only has an integer as property
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\ORMObject;

/**
 * Only for testing purposes
 * @author klaus
 */
class DummyChild extends Dummy {
	
    protected static function setupProperties() 
    {
		parent::setupProperties();
		self::integer('dummychildint')->searchable();
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'dummychild');
	    static::addInfo('table', 'dummychildren');
	    static::addInfo('name_s', 'dummychild');
	    static::addInfo('name_p', 'dummychildren');
	    static::addInfo('description', 'A dummy child test object class that only provides an integer');
	    static::addInfo('options', 0);
	}
	
}
