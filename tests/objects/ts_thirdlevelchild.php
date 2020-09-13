<?php

namespace Sunhill\ORM\Test;

class ts_thirdlevelchild extends ts_secondlevelchild {
    public static $table_name = 'thirdlevelchildren';
    public static $object_infos = [
        'name'=>'thiredlevelchild',       // A repetition of static:$object_name @todo see above
        'table'=>'thridlevelchildren',     // A repitition of static:$table_name
        'name_s'=>'third level child',     // A human readable name in singular
        'name_p'=>'third level children',    // A human readable name in plural
        'description'=>'Another test class. A derrived class with all some properties',
        'options'=>0,           // Reserved for later purposes
    ];
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('childchildint');
		self::object('thirdlevelobject')->set_allowed_objects(["\\Sunhill\\ORM\\Objects\\oo_object"]);
		self::arrayofstrings('thirdlevelsarray');
	}
	
	public function post_promotion($from) {
	    if (is_a($from,'Sunhill\ORM\Test\ts_secondlevelchild')) {
	        $this->childchildint = $this->childint * 2;
	    } elseif (is_a($from,'Sunhill\ORM\Test\ts_passthru')) {	        
	        $this->childint = 2;
	        $this->childchildint = $this->childint * 2;	        
	    } else {
	        throw new \Exception("OOPS");
	    }
	}
}

