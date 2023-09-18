<?php
/**
 * @file Circular.php
 * Provides the test object Circular for testing circular references
 * Lang en
 * Reviewstatus: 2023-09-18
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\PropertyList;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;

class Circular extends ORMObject 
{
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('payload');
        $list->object('parent')->setAllowedClasses('circular');
        $list->object('child')->setAllowedClasses('circular');
    }

	protected static function setupInfos()
	{
	    static::addInfo('name', 'circular');
	    static::addInfo('table', 'circulars');
	    static::addInfo('name_s', 'circular');
	    static::addInfo('name_p', 'circular');
	    static::addInfo('description', 'Another test class. A class that referes to itself');
	    static::addInfo('options', 0);
	}
	
}
