<?php

namespace Sunhill\ORM\Test;

use Sunhill\ORM\Objects;

class ts_testparent extends \Sunhill\ORM\Objects\oo_object {
    public static $table_name = 'testparents';
    public static $object_infos = [
        'name'=>'testparent',       // A repetition of static:$object_name @todo see above
        'table'=>'testparents',     // A repitition of static:$table_name
        'name_s'=>'test parent',     // A human readable name in singular
        'name_p'=>'test parents',    // A human readable name in plural
        'description'=>'Another test class. A class with all avaiable properties',
        'options'=>0,           // Reserved for later purposes
    ];
    
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
		self::object('parentobject')->set_allowed_objects(['dummy'])->set_default(null);
		self::arrayofstrings('parentsarray');
		self::arrayofobjects('parentoarray')->set_allowed_objects(['dummy']);
		self::calculated('parentcalc');
	}
	
	public function calculate_parentcalc() {
	    return $this->parentint."A";
	}
}

