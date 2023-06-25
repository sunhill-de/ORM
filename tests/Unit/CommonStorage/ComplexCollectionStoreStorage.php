<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyVarchar;

class ComplexCollectionStoreStorage extends TestStorage
{
    
    public function __construct()
    {
        $this->createEntity('field_int','complexcollections')->setType(PropertyInteger::class)->setValue(333);    
        $this->createEntity('field_char','complexcollections')->setType(PropertyVarchar::class)->setValue('ABC');
        $this->createEntity('field_float','complexcollections')->setType(PropertyFloat::class)->setValue(1.23);
        $this->createEntity('field_text','complexcollections')->setType(PropertyText::class)->setValue('Lorem ipsum');
        $this->createEntity('field_datetime','complexcollections')->setType(PropertyDatetime::class)->setValue( '2023-05-10 11:43:00');
        $this->createEntity('field_date','complexcollections')->setType(PropertyDate::class)->setValue( '2023-05-10');
        $this->createEntity('field_time','complexcollections')->setType(PropertyTime::class)->setValue( '11:43:00');
        $this->createEntity('field_enum','complexcollections')->setType(PropertyEnum::class)->setValue( 'testC');
        $this->createEntity('field_object','complexcollections')->setType(PropertyObject::class)->setValue( 1);
        $this->createEntity('field_oarray','complexcollections')->setType(PropertyArray::class)->setValue( [2,3,4] );
        $this->createEntity('field_sarray','complexcollections')->setType(PropertyArray::class)->setValue( ['AAA','BBB','CCC']);
        $this->createEntity('field_smap','complexcollections')->setType(PropertyMap::class)->setValue( ['KeyA'=>'ValueA','KeyB'=>'ValueB']);
        $this->createEntity('field_calc','complexcollections')->setType(PropertyCalculated::class)->setValue( '333A' );
    }
    
}