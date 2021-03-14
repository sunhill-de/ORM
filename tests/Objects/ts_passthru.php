<?php

namespace Sunhill\ORM\Tests\Objects;

class ts_passthru extends ts_testparent {

    protected static $property_definitions;
    
    public static $table_name = 'passthrus';

    public static $object_infos = [
        'name'=>'passthru',       // A repetition of static:$object_name @todo see above
        'table'=>'passthrus',     // A repitition of static:$table_name
        'name_s'=>'passthru',     // A human readable name in singular
        'name_p'=>'passthrus',    // A human readable name in plural
        'description'=>'Another test class. A class with no own properties',
        'options'=>0,           // Reserved for later purposes
    ];
    
}

