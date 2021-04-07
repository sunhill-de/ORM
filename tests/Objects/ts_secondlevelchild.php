<?php

namespace Sunhill\ORM\Tests\Objects;

class ts_secondlevelchild extends ts_passthru {
    public static $table_name = 'secondlevelchildren';
    
    public static $object_infos = [
        'name'=>'secondlevelchild',       // A repetition of static:$object_name @todo see above
        'table'=>'secondlevelchildren',     // A repitition of static:$table_name
        'name_s'=>'second level child',     // A human readable name in singular
        'name_p'=>'second level children',    // A human readable name in plural
        'description'=>'Another test class. A derrived class',
        'options'=>0,           // Reserved for later purposes
    ];
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('childint');
	}
	
}

