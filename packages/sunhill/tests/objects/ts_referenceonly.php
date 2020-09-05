<?php
namespace Sunhill\Test;

class ts_referenceonly extends \Sunhill\Objects\oo_object {
    public static $table_name = 'referenceonlies';
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('testint');
		self::object('testobject')->set_allowed_objects(['\Sunhill\test\ts_dummy','\Sunhill\test\ts_referenceonly'])->set_default(null);;
		self::arrayofobjects('testoarray')->set_allowed_objects(['\Sunhill\test\ts_dummy','\Sunhill\test\ts_referenceonly']);
	}
	
}

