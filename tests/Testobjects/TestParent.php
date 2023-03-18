<?php

namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\ORMObject;

class TestParent extends ORMObject {
    
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
    protected static function setupProperties() {
		parent::setupProperties();
		self::integer('parentint')->searchable();
		self::varchar('parentchar')->searchable()->nullable();
		self::float('parentfloat')->searchable();
		self::text('parenttext')->searchable();
		self::datetime('parentdatetime')->searchable();
		self::date('parentdate')->searchable();
		self::time('parenttime')->searchable();
		self::enum('parentenum')->setValues(['testA','testB','testC'])->searchable();
		self::object('parentobject')->setAllowedObjects(['dummy'])->setDefault(null)->searchable();
		self::arrayofstrings('parentsarray')->searchable();
		self::arrayOfObjects('parentoarray')->setAllowedObjects(['dummy'])->searchable();
		self::calculated('parentcalc')->searchable();
		self::integer('nosearch')->setDefault(1);
	}
	
	public function calculate_parentcalc() {
	    return $this->parentint."A";
	}
}

