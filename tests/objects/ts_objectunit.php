<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class FakeStorage extends \Sunhill\Storage\storage_base {
    
    protected function execute_chain(string $chainname,int $id) {
        switch ($chainname) {
            case 'load':
                $this->entities = $this->caller->storage_values;
                break;
        }
        return 1;
    }
    
    
}

class ts_objectunit extends \Sunhill\Objects\oo_object {
	
    public static $table_name = 'objectunits';

    public $storage_values;
    
    protected function create_storage() {
        return new FakeStorage($this);
    }
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('intvalue');
        self::object('objectvalue')->set_allowed_objects(['\Sunhill\test\ts_dummy'])->set_default(null);
        self::arrayofstrings('sarray');
        self::arrayofobjects('oarray')->set_allowed_objects(['\Sunhill\Test\ts_dummy']);
        self::calculated('calcvalue');
    }

    public function calculate_calcvalue() {
        return $this->intvalue."A";
    }

    public function public_load($id) {
        $this->load($id);
    }
}

