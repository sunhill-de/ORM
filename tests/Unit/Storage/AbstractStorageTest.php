<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Storage\AbstractStorage;
use Sunhill\ORM\Tests\TestSupport\TestAbstractStorage;

class AbstractStorageTest extends TestCase
{

    public function testReadValue()
    {
        $test = new TestAbstractStorage();
        $this->assertEquals('TESTVALUE', $test->getValue('test'));
    }
    
    public function testWriteValue()
    {
        $test = new TestAbstractStorage();
        $test->setValue('new','NEWVALUE');
        $this->assertEquals('NEWVALUE', $test->getValue('new'));
    }
    
    public function testUpdateValue()
    {
        $test = new TestAbstractStorage();
        $test->setValue('test', 'NEWVALUE');
        $this->assertEquals('NEWVALUE', $test->getValue('test'));
    }
}