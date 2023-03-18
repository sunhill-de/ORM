<?php

namespace Sunhill\ORM\Tests\Testobjects;

class TestSimpleChild extends TestParent {

    protected static $property_definitions;
    
    public static $table_name = 'testsimplechildren';

    public static $object_infos = [
        'name'=>'testsimplechild',       // A repetition of static:$object_name @todo see above
        'table'=>'testsimplechildren',     // A repitition of static:$table_name
        'name_s'=>'test simple child',     // A human readable name in singular
        'name_p'=>'test simple children',    // A human readable name in plural
        'description'=>'Another test class. A class with no own properties',
        'options'=>0,           // Reserved for later purposes
    ];
    
}

