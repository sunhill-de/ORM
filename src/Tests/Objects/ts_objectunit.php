<?php

namespace Sunhill\ORM\Tests\Objects;

use Sunhill\ORM\Objects;

class FakeStorage extends \Sunhill\ORM\Storage\storage_base {
    
    protected function execute_chain(string $chainname,int $id, $payload=null) {
        switch ($chainname) {
            case 'load':
                $this->entities = $this->caller->storage_values;
                break;
            case 'insert':
                if (isset($this->entities['attributes'])) {
                    foreach ($this->entities['attributes'] as $attribute) {
                        $this->entities['attributes'][$attribute['name']]['value_id'] = 9;
                    }
                }
            case 'update':
                $this->caller->storage_values = $this->entities;
                break;
        }
        return 1;
    }
    
    public function execute_need_id_queries() {
        
    }
    
}

class ts_objectunit extends \Sunhill\ORM\Objects\ORMObject {
	
    public static $table_name = 'objectunits';

    public static $object_infos = [
        'name'=>'objectunit',       // A repetition of static:$object_name @todo see above
        'table'=>'objectunits',     // A repitition of static:$table_name
        'name_s'=>'object unit',     // A human readable name in singular
        'name_p'=>'object units',    // A human readable name in plural
        'description'=>'Another test class',
        'options'=>0,           // Reserved for later purposes
    ];
    public $storage_values;
    
    protected function create_storage() {
        return new FakeStorage($this);
    }
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('intvalue');
        self::object('objectvalue')->set_allowed_objects(['dummy'])->set_default(null);
        self::arrayofstrings('sarray');
        self::arrayofobjects('oarray')->set_allowed_objects(['dummy']);
        self::calculated('calcvalue');
    }

    public function calculate_calcvalue() {
        return $this->intvalue."A";
    }

    public function public_load($id) {
        $this->load($id);
    }
}

