<?php
/**
 * @file testA.php
 * Provides the test object testA. Only for migration tests
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Test;

use Sunhill\ORM\Objects\oo_object;

class testE extends oo_object {
    
    public static $object_infos = [
        'name'=>'testE',            // A repetition of static:$object_name @todo see above
        'table'=>'testE',         // A repitition of static:$table_name
        'name_s'=>'Migrationtest e object',   // A human readable name in singular
        'name_p'=>'Migrationtest e objects',  // A human readable name in plural
        'description'=>'For migration tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'testE';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::arrayofobjects('testfield')->set_allowed_objects(["dummy"]);
    }
    
}

