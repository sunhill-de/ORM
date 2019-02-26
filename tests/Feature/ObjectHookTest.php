<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class HookedObject extends \Sunhill\Objects\oo_object {
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('hooked_int')->set_model('\Tests\Feature\Hooked');
    }
    
}

class HookingObject extends \Sunhill\Objects\oo_object  {
    
}

class ObjectHookTest extends ObjectCommon
{
            
}
