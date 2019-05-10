<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_testparent extends \Sunhill\Objects\oo_object {
    public static $table_name = 'testparents';
    
    public static $flag = '';
    
    public $trigger_exception = false;
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('parentint');
		self::varchar('parentchar');
		self::float('parentfloat');
		self::text('parenttext');
		self::datetime('parentdatetime');
		self::date('parentdate');
		self::time('parenttime');
		self::enum('parentenum')->set_values(['testA','testB','testC']);
		self::object('parentobject')->set_allowed_objects(['\Sunhill\test\ts_dummy'])->set_default(null);
		self::arrayofstrings('parentsarray');
		self::arrayofobjects('parentoarray')->set_allowed_objects(['\Sunhill\Test\ts_dummy']);
	}
	
}

