<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;

class DummyLoadStorage extends TestStorage
{
    
    protected function setObjectValues()
    {
        $this->setValue('_created_at','2019-05-15 10:00:00');
        $this->setValue('_updated_at','2019-05-15 10:00:00');
        $this->setValue('_uuid','a123');
        $this->setValue('_owner',0);
        $this->setValue('_group',0);
        $this->setValue('_read',7);
        $this->setValue('_edit',7);
        $this->setValue('_delete',7);
    }
    
    public function __construct()
    {
        $this->setObjectValues();
        
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