<?php
/**
 * @file DummyCollection.php
 * Provides the test collection dummycollection
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Objects\PropertyList;

/**
 * Only for testing purposes
 * @author klaus
 */
class AnotherDummyCollection extends Collection {
	
    protected static function setupProperties(PropertyList $list)
    {
		$list->integer('dummyint')->searchable();
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'anotherdummycollection');
	    static::addInfo('table', 'anotherdummycollections');
	    static::addInfo('name_s', 'another dummy collection');
	    static::addInfo('name_p', 'another dummies collection');
	    static::addInfo('description', 'Another dummy test collection that provides an integer');
	    static::addInfo('options', 0);
	}
	
}

