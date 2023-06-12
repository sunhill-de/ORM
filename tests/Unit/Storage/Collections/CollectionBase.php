<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;

class CollectionBase extends DatabaseTestCase
{
    
    protected function getDummyStorage()
    {
        $test = new MysqlStorage();
        $test->setSourceType('collection');
        $test->setEntity('dummyint', null, 'dummycollections', PropertyInteger::class, null);
        
        return $test;
    }
    
    protected function getComplexStorage()
    {
        $test = new MysqlStorage();
        $test->setSourceType('collection');
        $test->setEntity('field_int', null, 'complexcollections', PropertyInteger::class,null);
        $test->setEntity('field_char', null, 'complexcollections', PropertyVarchar::class,null);
        $test->setEntity('field_float', null, 'complexcollections', PropertyFloat::class,null);
        $test->setEntity('field_text', null, 'complexcollections', PropertyText::class,null);
        $test->setEntity('field_datetime', null, 'complexcollections', PropertyDatetime::class,null);
        $test->setEntity('field_date', null, 'complexcollections', PropertyDate::class,null);
        $test->setEntity('field_time', null, 'complexcollections', PropertyTime::class,null);
        $test->setEntity('field_enum', null, 'complexcollections', PropertyEnum::class,null);
        $test->setEntity('field_object', null, 'complexcollections', PropertyObject::class,null);
        $test->setEntity('field_calc', null, 'complexcollections', PropertyCalculated::class,null);
        $test->setEntity('field_oarray', null, 'complexcollections', PropertyArray::class, [], null);
        $test->setEntity('field_sarray', null, 'complexcollections', PropertyArray::class, [], null);
        $test->setEntity('field_smap', null, 'complexcollections', PropertyMap::class, [], null);
        
        return $test;
    }
        
}