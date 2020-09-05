<?php
namespace Sunhill\Test;

class ts_testchild extends ts_testparent {
    public static $table_name = 'testchildren';
    
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
		self::object('childobject')->set_allowed_objects(['\Sunhill\test\ts_dummy'])->set_default(null);;
		self::arrayofstrings('childsarray');
		self::arrayofobjects('childoarray')->set_allowed_objects(['\Sunhill\test\ts_dummy']);
	}
	
}

