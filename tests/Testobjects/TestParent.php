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

class TestParent extends ORMObject 
{
        
    public static $flag = '';
    
    public $trigger_exception = false;
    
    protected static function setupProperties() 
    {
		parent::setupProperties();
		self::integer('parentint')->searchable();
		self::varchar('parentchar')->searchable()->setDefault(null);
		self::float('parentfloat')->searchable();
		self::text('parenttext')->searchable();
		self::datetime('parentdatetime')->searchable();
		self::date('parentdate')->searchable();
		self::time('parenttime')->searchable();
		self::enum('parentenum')->setValues(['testA','testB','testC'])->searchable();
		self::object('parentobject')->setAllowedObjects(['dummy'])->setDefault(null)->searchable();
		self::arrayofstrings('parentsarray')->searchable();
		self::arrayOfObjects('parentoarray')->setAllowedObjects(['dummy'])->searchable();
		self::calculated('parentcalc')->searchable();
		self::integer('nosearch')->setDefault(1);
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
