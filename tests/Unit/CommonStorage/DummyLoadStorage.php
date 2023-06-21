<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;

class DummyLoadStorage extends TestStorage
{
    
    public function __construct()
    {
        $this->setValue('dummyint', 123);
        $this->setValue('tags',[1,2,4]);
        $entry = new \StdClass();
        $entry->allowed_classes = '|objects|';
        $entry->name = 'general_attribute';
        $entry->attribute_id = 4;
        $entry->type = 'integer';
        $entry->value = 444;
        $this->setValue('attributes',[$entry]);
    }
    
}