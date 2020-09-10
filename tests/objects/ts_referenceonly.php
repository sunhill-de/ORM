<?php
namespace Sunhill\ORM\Test;

class ts_referenceonly extends \Sunhill\ORM\Objects\oo_object {
    public static $table_name = 'referenceonlies';
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('testint');
		self::object('testobject')->set_allowed_objects(['\Sunhill\ORM\Test\ts_dummy','\Sunhill\ORM\Test\ts_referenceonly'])->set_default(null);;
		self::arrayofobjects('testoarray')->set_allowed_objects(['\Sunhill\ORM\Test\ts_dummy','\Sunhill\ORM\Test\ts_referenceonly']);
	}
	
}

