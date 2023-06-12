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
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyVarchar;

/**
 * Only for testing purposes
 * @author klaus
 */
class ComplexCollection extends Collection {
	
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('field_int')->searchable();
        $list->varchar('field_char')->searchable()->setDefault(null);
        $list->float('field_float')->searchable();
        $list->text('field_text')->searchable();
        $list->datetime('field_datetime')->searchable();
        $list->date('field_date')->searchable();
        $list->time('field_time')->searchable();
        $list->enum('field_enum')->setValues(['testA','testB','testC'])->searchable();
        $list->object('field_object')->setAllowedObjects(['dummy'])->setDefault(null)->searchable();
        $list->array('field_sarray', PropertyVarchar::class)->searchable();
        $list->array('field_oarray', PropertyObject::class)->setAllowedObjects(['dummy'])->searchable();
        $list->map('field_smap', PropertyVarchar::class);
        $list->calculated('field_calc')->searchable();
    }

	protected static function setupInfos()
	{
	    static::addInfo('name', 'compelxcollection');
	    static::addInfo('table', 'complexcollections');
	    static::addInfo('description', 'A more complex collection.');
	    static::addInfo('options', 0);
	}
	
}
