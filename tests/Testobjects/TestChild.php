<?php
namespace Sunhill\ORM\Tests\Testobjects;

class TestChild extends TestParent {
    public static $table_name = 'testchildren';
    
    public static $object_infos = [
        'name'=>'testchild',       // A repetition of static:$object_name @todo see above
        'table'=>'testchildren',     // A repitition of static:$table_name
        'name_s'=>'test child',     // A human readable name in singular
        'name_p'=>'test children',    // A human readable name in plural
        'description'=>'Another test class. A derrived class with all avaiable properties',
        'options'=>0,           // Reserved for later purposes
    ];
    protected static $property_definitions;
    protected static function setupProperties() {
	    parent::setupProperties();
	    self::integer('childint')->searchable();
	    self::varchar('childchar')->searchable()->nullable();
	    self::float('childfloat')->searchable();
	    self::text('childtext');
	    self::datetime('childdatetime');
	    self::date('childdate');
		self::time('childtime');
		self::enum('childenum')->setValues(['testA','testB','testC']);
		self::object('childobject')->setAllowedObjects(['dummy'])->setDefault(null);;
		self::arrayofstrings('childsarray');
		self::arrayOfObjects('childoarray')->setAllowedObjects(['dummy']);
		self::calculated('childcalc')->searchable();
    }

	public function calculate_childcalc() {
	    return $this->childint."B";
	}
	
}

