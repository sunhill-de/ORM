<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;

class DummyCollectionLoadStorage extends TestStorage
{
    
    public function __construct()
    {
        $this->setValue('dummyint', 123);
    }
    
}