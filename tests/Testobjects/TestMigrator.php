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

use Sunhill\ORM\Objects\PropertyList;

class TestMigrator extends TestParent {

    protected static function setupProperties(PropertyList $list)
    {
	    $list->integer('migratorint')->searchable();
	    $list->varchar('migratorchar')->searchable()->nullable();
	    $list->float('migratorfloat')->searchable();
	    $list->text('migratortext');
	    $list->datetime('migratordatetime');
	    $list->date('migratordate');
		$list->time('migratortime');
		$list->enum('migratorenum')->setValues(['testA','testB','testC']);
		$list->object('migratorobject')->setAllowedObjects(['dummy'])->setDefault(null);;
		$list->arrayofstrings('migratorsarray');
		$list->arrayOfObjects('migratoroarray')->setAllowedObjects(['dummy']);
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

