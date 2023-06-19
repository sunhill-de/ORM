<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Tests\Utils\TestStorage;

class UpdateTest extends DatabaseTestCase
{
    
    /**
     * @group storecollection
     */
    public function testDummyCollectionUpdate()
    {
        $test = new DummyCollection();
        $storage = new TestStorage();
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->dummyint = 988;
        $test->commit();
        
        $this->assertEquals('stored', $storage->state);
        $this->assertEquals(988, $storage->dummyint);
    }
    
}