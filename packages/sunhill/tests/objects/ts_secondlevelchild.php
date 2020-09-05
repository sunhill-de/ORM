<?php

namespace Sunhill\Test;

class ts_secondlevelchild extends ts_passthru {
    public static $table_name = 'secondlevelchildren';
    
    protected static $property_definitions;
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('childint');
	}
	
}

