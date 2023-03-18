<?php
/**
 * @file Dummy.php
 * Provides the test object Dummy that only has an integer as property
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

use Sunhill\ORM\Objects\ORMObject;

/**
 * Only for testing purposes
 * @author klaus
 */
class Dummy extends ORMObject {
	
    public static $table_name = 'dummies';
    
    public static $object_infos = [
        'name'=>'dummy',            // A repetition of static:$object_name @todo see above
        'table'=>'dummies',         // A repitition of static:$table_name
        'name_s'=>'dummy object',   // A human readable name in singular
        'name_p'=>'dummy objects',  // A human readable name in plural
        'description'=>'A dummy test object class that only provides an integer',
        'options'=>0,               // Reserved for later purposes
    ];
        
    protected static function setupProperties() {
		parent::setupProperties();
		self::integer('dummyint')->searchable();
	}
	
}

