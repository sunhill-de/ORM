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

class TestChild extends TestParent 
{
   
    protected static function setupProperties() 
    {
	    parent::setupProperties();
	    self::integer('childint')->searchable();
	    self::varchar('childchar')->searchable()->nullable();
	    self::float('childfloat')->searchable();
	    self::text('childtext');
	    self::datetime('childdatetime');
	    self::date('childdate');
		self::time('childtime');
		self::enum('childenum')->setValues(['testA','testB','testC']);
		self::object('childobject')->setAllowedObjects(['dummy'])->setDefault(null);;
		self::arrayofstrings('childsarray');
		self::arrayOfObjects('childoarray')->setAllowedObjects(['dummy']);
		self::calculated('childcalc')->searchable();
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
