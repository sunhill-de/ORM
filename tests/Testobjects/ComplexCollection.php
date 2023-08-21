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
        $list->boolean('field_bool')->searchable();
        $list->varchar('field_char')->setMaxLen(20)->searchable()->setDefault(null);
        $list->float('field_float')->searchable();
        $list->text('field_text')->searchable();
        $list->datetime('field_datetime')->searchable();
        $list->date('field_date')->searchable();
        $list->time('field_time')->searchable();
        $list->enum('field_enum')->setValues(['testA','testB','testC'])->searchable();
        $list->object('field_object')->setAllowedClasses(['dummy'])->setDefault(null)->searchable();
        $list->collection('field_collection')->setAllowedCollection(DummyCollection::class);
        $list->array('field_sarray')->setElementType(PropertyVarchar::class)->searchable();
        $list->array('field_oarray')->setElementType(PropertyObject::class)->setAllowedClasses(['dummy'])->searchable();
        $list->map('field_smap')->setElementType(PropertyVarchar::class);
        $list->calculated('field_calc')->setCallback(function($object) { return $object->field_int.'A'; })->searchable();
        $list->integer('nosearch')->setDefault(1);
    }

	protected static function setupInfos()
	{
	    static::addInfo('name', 'compelxcollection');
	    static::addInfo('table', 'complexcollections');
	    static::addInfo('description', 'A more complex collection.');
	    static::addInfo('options', 0);
	}
	
}

