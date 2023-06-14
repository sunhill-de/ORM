<?php
/**
 * @file TestChild.php
 * Provides the test object TestChild that defines all possible prooperties
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\PropertyList;

class TestChild extends TestParent 
{
   
    protected static function setupProperties(PropertyList $list)
    {
	    $list->integer('childint')->searchable();
	    $list->varchar('childchar')->searchable()->default(null);
	    $list->float('childfloat')->searchable();
	    $list->text('childtext')->searchable();
	    $list->datetime('childdatetime')->searchable();
	    $list->date('childdate')->searchable();
		$list->time('childtime')->searchable();
		$list->enum('childenum')->setValues(['testA','testB','testC'])->searchable();
		$list->object('childobject')->setAllowedClasses(['dummy'])->setDefault(null)->searchable();
		$list->arrayofstrings('childsarray')->searchable();
		$list->arrayOfObjects('childoarray')->setAllowedClasses(['dummy'])->searchable();
		$list->calculated('childcalc')->searchable();
    }

	public function calculate_childcalc() 
	{
	    return $this->childint."B";
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'testchild');
	    static::addInfo('table', 'testchildren');
	    static::addInfo('name_s', 'test child');
	    static::addInfo('name_p', 'test child');
	    static::addInfo('description', 'Another test class. A derrived class with all avaiable properties');
	    static::addInfo('options', 0);
	}
	
}
