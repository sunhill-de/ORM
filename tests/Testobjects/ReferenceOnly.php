<?php
/**
 * @file ReferenceOnly.php
 * Provides the test object ReferenceOnly that has no simple fields
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
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyObject;

class ReferenceOnly extends ORMObject 
{
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->array('testsarray')->setElementType(PropertyVarchar::class);
        $list->array('testoarray')->setElementType(PropertyObject::class)->setAllowedClasses(['dummy','referenceonly']);
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'referenceonly');
	    static::addInfo('table', 'referenceonlies');
	    static::addInfo('name_s', 'referenceonly');
	    static::addInfo('name_p', 'referenceonlies');
	    static::addInfo('description', 'Another test class. A class that only defines reference properties (no simple ones)');
	    static::addInfo('options', 0);
	}
	
}
