<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;

class ORMObjectLoadTest extends TestCase
{

    public function testDummy()
    {
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);
        $this->assertEquals('2023-05-05 10:00:00', $test->created_at);
        $this->assertEquals(0, $test->obj_owner);
    }
}