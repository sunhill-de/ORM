<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;

class ComplexCollectionLoadStorage extends TestStorage
{
    
    public function __construct()
    {
        $this->setValue('field_int',123);
        $this->setValue('field_char','ABC');
        $this->setValue('field_float',1.23);
        $this->setValue('field_text','Lorem ipsum');
        $this->setValue('field_datetime', '2023-05-10 11:43:00');
        $this->setValue('field_date', '2023-05-10');
        $this->setValue('field_time', '11:43:00');
        $this->setValue('field_enum', 'testC');
        $this->setValue('field_object', 1);
        $this->setValue('field_oarray', [2,3,4]);
        $this->setValue('field_sarray', ['AAA','BBB','CCC']);
        $this->setValue('field_smap', ['KeyA'=>'ValueA','KeyB'=>'ValueB']);
        $this->setValue('field_calc', '123A');
    }
    
}