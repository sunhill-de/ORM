<?php

namespace Sunhill\ORM\Tests\Objects;

class SecondLevelChild extends Passthru {
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
    protected static function setupProperties() {
		parent::setupProperties();
		self::integer('childint');
	}
	
}

