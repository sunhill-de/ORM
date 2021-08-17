<?php
/**
 * @file SimpleParent.php
 * Provides the test object testA. Only for migration tests
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Objects;

use Sunhill\ORM\Objects\oo_object;

class SimpleChild extends SimpleParent {
    
    public static $object_infos = [
        'name'=>'SimpleChild',            // A repetition of static:$object_name @todo see above
        'table'=>'simplechildren',         // A repitition of static:$table_name
        'name_s'=>'Simple hirarchic object',   // A human readable name in singular
        'name_p'=>'Simple hirarchic objects',  // A human readable name in plural
        'description'=>'For testing only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'simpleparents';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('childint');
        self::varchar('childchar');
    }
    
}

