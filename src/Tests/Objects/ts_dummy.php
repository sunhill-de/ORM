<?php
/**
 * @file ts_dummy.php
 * Provides the test object ts_dummy that only has an integer as property
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Objects;

use Sunhill\ORM\Objects\ORMObject;

/**
 * Only for testing purposes
 * @author klaus
 */
class ts_dummy extends ORMObject {
	
    public static $table_name = 'dummies';
    
    public static $object_infos = [
        'name'=>'dummy',            // A repetition of static:$object_name @todo see above
        'table'=>'dummies',         // A repitition of static:$table_name
        'name_s'=>'dummy object',   // A human readable name in singular
        'name_p'=>'dummy objects',  // A human readable name in plural
        'description'=>'A dummy test object class that only provides an integer',
        'options'=>0,               // Reserved for later purposes
    ];
    
    public $changestr = '';
    
    protected static $property_definitions;
    
    protected static function setup_properties() {
		parent::setup_properties();
		self::integer('dummyint')->searchable();
	}
	
	protected function setup_hooks() {
	    parent::setup_hooks();
	    $this->addHook('UPDATED_PROPERTY','tag_changed','tags');
	}
	public function tag_changed($change) {
	    if (!empty($change['NEW'])) {
	        $this->changestr .= 'ADD:';
	        foreach ($change['NEW'] as $tag) {
    	       $this->changestr .= $tag->getFullPath();       
    	    }
	    }
	    if (!empty($change['REMOVED'])) {
	        $this->changestr .= 'REMOVED:';
	        foreach ($change['REMOVED'] as $tag) {
	            $this->changestr .= $tag->getFullPath();
	        }
	    }
	}
	
}

