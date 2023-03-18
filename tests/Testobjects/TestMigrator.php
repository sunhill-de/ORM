<?php
namespace Sunhill\ORM\Tests\Testobjects;

class TestMigrator extends TestParent {
    public static $table_name = 'testchildren';
    
    public static $object_infos = [
        'name'=>'testmigrator',       // A repetition of static:$object_name @todo see above
        'table'=>'testmigrators',     // A repitition of static:$table_name
        'name_s'=>'test migrator',     // A human readable name in singular
        'name_p'=>'test migrators',    // A human readable name in plural
        'description'=>'Another test class. A derrived class with all avaiable properties',
        'options'=>0,           // Reserved for later purposes
    ];

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
	
}

