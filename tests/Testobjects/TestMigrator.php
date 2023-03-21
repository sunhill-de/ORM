<?php
/**
 * @file TestMigrator.php
 * Provides the test object TestMigrator that is derrived from TestParent
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

class TestMigrator extends TestParent {

    protected static function setupProperties() 
    {
	    parent::setupProperties();
	    self::integer('migratorint')->searchable();
	    self::varchar('migratorchar')->searchable()->nullable();
	    self::float('migratorfloat')->searchable();
	    self::text('migratortext');
	    self::datetime('migratordatetime');
	    self::date('migratordate');
		self::time('migratortime');
		self::enum('migratorenum')->setValues(['testA','testB','testC']);
		self::object('migratorobject')->setAllowedObjects(['dummy'])->setDefault(null);;
		self::arrayofstrings('migratorsarray');
		self::arrayOfObjects('migratoroarray')->setAllowedObjects(['dummy']);
	}

	protected static function setupInfos()
	{
	    static::addInfo('name', 'testmigrator');
	    static::addInfo('table', 'testmigrators');
	    static::addInfo('name_s', 'testmigrator');
	    static::addInfo('name_p', 'testmigrator');
	    static::addInfo('description', 'Another test class. A derrived class with all avaiable properties');
	    static::addInfo('options', 0);
	}
	
}

