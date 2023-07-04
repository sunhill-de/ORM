<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Utils;

use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;

trait CollectionsAndObjects 
{

    protected function getObject($id)
    {
        $test = new Dummy();
        $test->setID($id);
        return $test;
    }
    
    protected function getCollection($id)
    {
        $test = new DummyCollection();
        $test->setID($id);
        return $test;
    }
        
}