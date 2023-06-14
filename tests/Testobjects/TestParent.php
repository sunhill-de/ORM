<?php
/**
 * @file TestParent.php
 * Provides the test object TestParent that define all possible properties
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
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyVarchar;

class TestParent extends ORMObject 
{
        
    public static $flag = '';
    
    public $trigger_exception = false;
    
    protected static function setupProperties(PropertyList $list)
    {
		$list->integer('parentint')->searchable();
		$list->varchar('parentchar')->searchable()->setDefault(null);
		$list->float('parentfloat')->searchable();
		$list->text('parenttext')->searchable();
		$list->datetime('parentdatetime')->searchable();
		$list->date('parentdate')->searchable();
		$list->time('parenttime')->searchable();
		$list->enum('parentenum')->setValues(['testA','testB','testC'])->searchable();
		$list->boolean('parentbool');
		$list->object('parentobject')->setAllowedClasses(['dummy'])->setDefault(null)->searchable();
		$list->array('parentsarray')->setElementType(PropertyVarchar::class)->searchable();
		$list->array('parentoarray')->setElementType(PropertyObject::class)->setAllowedClasses(['dummy'])->searchable();
		$list->calculated('parentcalc')->searchable();
		$list->collection('parentcollection')->setAllowedClasses(DummyCollection::class)->searchable();
		$list->keyfield('parentkeyfield',':parentchar (:parentint)')->searchable();
		$list->map('parentmap')->setElementType(PropertyVarchar::class)->searchable();
		$list->externalReference('parent_external', 'external', 'id_field')->setInternalKey('id');
		$list->integer('nosearch')->setDefault(1);
	}
	
	public function calculate_parentcalc() 
	{
	    return $this->parentint."A";
	}
	
	protected static function setupInfos()
	{
	    static::addInfo('name', 'testparent');
	    static::addInfo('table', 'testparents');
	    static::addInfo('description', 'Another test class. A class with all avaiable properties');
	    static::addInfo('options', 0);
	}
}
