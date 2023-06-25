<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;
use Sunhill\ORM\Properties\PropertyInteger;

class DummyCollectionStoreStorage extends TestStorage
{
    
    public function __construct()
    {
        $this->createEntity('dummyint', 'dummycollections')
        ->setType(PropertyInteger::class)
        ->setValue(333);
    }
    
}