<?php
namespace Sunhill\ORM\Tests\Objects;

class ReferenceOnly extends \Sunhill\ORM\Objects\ORMObject {
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
    protected static function setupProperties() {
		parent::setupProperties();
		self::integer('testint');
		self::object('testobject')->setAllowedObjects(['dummy','referenceonly'])->setDefault(null);;
		self::arrayOfObjects('testoarray')->setAllowedObjects(['dummy','referenceonly']);
	}
	
}

