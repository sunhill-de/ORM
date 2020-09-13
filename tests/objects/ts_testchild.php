<?php
namespace Sunhill\ORM\Test;

class ts_testchild extends ts_testparent {
    public static $table_name = 'testchildren';
    
    public static $object_infos = [
        'name'=>'testchild',       // A repetition of static:$object_name @todo see above
        'table'=>'testchildren',     // A repitition of static:$table_name
        'name_s'=>'test child',     // A human readable name in singular
        'name_p'=>'test children',    // A human readable name in plural
        'description'=>'Another test class. A derrived class with all avaiable properties',
        'options'=>0,           // Reserved for later purposes
    ];
    protected static $property_definitions;
    protected static function setup_properties() {
	    parent::setup_properties();
	    self::integer('childint')->searchable();
	    self::varchar('childchar')->searchable();
	    self::float('childfloat')->searchable();
	    self::text('childtext');
	    self::datetime('childdatetime');
	    self::date('childdate');
		self::time('childtime');
		self::enum('childenum')->set_values(['testA','testB','testC']);
		self::object('childobject')->set_allowed_objects(['\Sunhill\ORM\Test\ts_dummy'])->set_default(null);;
		self::arrayofstrings('childsarray');
		self::arrayofobjects('childoarray')->set_allowed_objects(['\Sunhill\ORM\Test\ts_dummy']);
	}
	
}

