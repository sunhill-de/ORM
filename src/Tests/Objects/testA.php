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
namespace Sunhill\ORM\Tests\Objects;

use Sunhill\ORM\Objects\ORMObject;

class testA extends ORMObject {
    
    public static $object_infos = [
        'name'=>'testA',            // A repetition of static:$object_name @todo see above
        'table'=>'testA',         // A repitition of static:$table_name
        'name_s'=>'Migrationtest A object',   // A human readable name in singular
        'name_p'=>'Migrationtest A objects',  // A human readable name in plural
        'description'=>'For migration tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'testA';
    
    protected static function setupProperties() {
        parent::setupProperties();
        self::integer('testint');
        self::varchar('testchar');
    }
    
}

