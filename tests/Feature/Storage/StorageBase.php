<?php

namespace Sunhill\ORM\Tests\Feature\Storage;

use Sunhill\ORM\Tests\DatabaseTestCase;

class StorageBase extends DatabaseTestCase
{
    static protected $is_prepared = false;
    
    
    protected function prepare_read() {
    }
    
    protected function prepare_write() {
    }

    
}
