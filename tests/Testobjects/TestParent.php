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
		$list->object('parentobject')->setAllowedObjects(['dummy'])->setDefault(null)->searchable();
		$list->arrayofstrings('parentsarray')->searchable();
		$list->arrayOfObjects('parentoarray')->setAllowedObjects(['dummy'])->searchable();
		$list->calculated('parentcalc')->searchable();
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
	    static::addInfo('name_s', 'test parent');
	    static::addInfo('name_p', 'test parents');
	    static::addInfo('description', 'Another test class. A class with all avaiable properties');
	    static::addInfo('options', 0);
	}
}
