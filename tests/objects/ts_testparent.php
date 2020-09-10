<?php

namespace Sunhill\ORM\Test;

use Sunhill\ORM\Objects;

class ts_testparent extends \Sunhill\ORM\Objects\oo_object {
    public static $table_name = 'testparents';
    
    public static $flag = '';
    
    public $trigger_exception = false;
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('parentint')->searchable();
		self::varchar('parentchar')->searchable();
		self::float('parentfloat')->searchable();
		self::text('parenttext');
		self::datetime('parentdatetime');
		self::date('parentdate');
		self::time('parenttime');
		self::enum('parentenum')->set_values(['testA','testB','testC']);
		self::object('parentobject')->set_allowed_objects(['\Sunhill\ORM\Test\ts_dummy'])->set_default(null);
		self::arrayofstrings('parentsarray');
		self::arrayofobjects('parentoarray')->set_allowed_objects(['\Sunhill\ORM\Test\ts_dummy']);
		self::calculated('parentcalc');
	}
	
	public function calculate_parentcalc() {
	    return $this->parentint."A";
	}
}

