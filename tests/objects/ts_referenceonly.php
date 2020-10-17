<?php
namespace Sunhill\ORM\Test;

class ts_referenceonly extends \Sunhill\ORM\Objects\oo_object {
    public static $table_name = 'referenceonlies';
    
    public static $object_infos = [
        'name'=>'referenceonly',       // A repetition of static:$object_name @todo see above
        'table'=>'referenceonlies',     // A repitition of static:$table_name
        'name_s'=>'reference only',     // A human readable name in singular
        'name_p'=>'reference onlies',    // A human readable name in plural
        'description'=>'Another test class. A class that only defines reference properties (no simple ones)',
        'options'=>0,           // Reserved for later purposes
    ];
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('testint');
		self::object('testobject')->set_allowed_objects(['dummy','referenceonly'])->set_default(null);;
		self::arrayofobjects('testoarray')->set_allowed_objects(['dummy','referenceonly']);
	}
	
}

