<?php
/**
 * @file testD.php
 * Provides the test object testD. Only for migration tests
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Objects;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class testD extends ts_dummy {
    
    public static $object_infos = [
        'name'=>'testD',            // A repetition of static:$object_name @todo see above
        'table'=>'testD',         // A repitition of static:$table_name
        'name_s'=>'Migrationtest D object',   // A human readable name in singular
        'name_p'=>'Migrationtest D objects',  // A human readable name in plural
        'description'=>'For migration tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'testD';
    
    public static $type='varchar';
    
    protected static function setup_properties() {
        $method = self::$type;
        parent::setup_properties();
        if ($method == 'enum') {
            self::enum('testfield')->setEnumValues(['A','B']);
        } else {
            self::$method('testfield');
        }
    }
    
}

