<?php

namespace Sunhill\ORM\Tests\Testobjects;

class ThirdLevelChild extends SecondLevelChild 
{
    public static $table_name = 'thirdlevelchildren';

    public static $object_infos = [
        'name'=>'thirdlevelchild',       // A repetition of static:$object_name @todo see above
        'table'=>'thirdlevelchildren',     // A repitition of static:$table_name
        'name_s'=>'third level child',     // A human readable name in singular
        'name_p'=>'third level children',    // A human readable name in plural
        'description'=>'Another test class. A derrived class with all some properties',
        'options'=>0,           // Reserved for later purposes
    ];
    
    protected static $property_definitions;
    
    protected static function setupProperties() 
    {
		parent::setupProperties();
		self::integer('childchildint');
		self::object('thirdlevelobject');
		self::arrayofstrings('thirdlevelsarray');
	}
	
	public function postPromotion($from) 
	{
	    if (is_a($from,'Sunhill\ORM\Tests\Objects\SecondLevelChild')) {
	        $this->childchildint = $this->childint * 2;
	    } elseif (is_a($from,'Sunhill\ORM\Tests\Objects\Passthru')) {	        
	        $this->childint = 2;
	        $this->childchildint = $this->childint * 2;	        
	    } else {
	        throw new \Exception("OOPS");
	    }
	}
}

