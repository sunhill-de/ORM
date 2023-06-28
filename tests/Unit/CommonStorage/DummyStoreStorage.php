<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyVarchar;

class DummyStoreStorage extends TestStorage
{
    
    protected function setObjectValues()
    {
        $this->createEntity('_created_at','objects')->setType(PropertyDatetime::class)->setValue('2023-06-26 11:32:29');
        $this->createEntity('_updated_at','objects')->setType(PropertyDatetime::class)->setValue('2023-06-26 11:32:29');
        $this->createEntity('_uuid','objects')->setType(PropertyVarchar::class)->setValue('6dcf5d47-8361-4a53-8b3b-7a2f0fe506c5');
        $this->createEntity('_owner','objects')->setType(PropertyInteger::class)->setValue(0);
        $this->createEntity('_group','objects')->setType(PropertyInteger::class)->setValue(0);
        $this->createEntity('_read','objects')->setType(PropertyInteger::class)->setValue(7);
        $this->createEntity('_edit','objects')->setType(PropertyInteger::class)->setValue(7);
        $this->createEntity('_delete','objects')->setType(PropertyInteger::class)->setValue(7);        
    }
    
    public function __construct()
    {
        $this->setObjectValues();
        $this->createEntity('dummyint', 'dummies')->setType(PropertyInteger::class)->setValue(2222);
    }
    
}