<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Tests\Utils\TestStorage;

use Sunhill\ORM\Properties\PropertyTags;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Unit\CommonStorage\DummyLoadStorage;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Tests\Unit\CommonStorage\TestParentLoadStorage;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Unit\CommonStorage\TestChildtLoadStorage;
use Sunhill\ORM\Tests\Unit\CommonStorage\TestChildLoadStorage;
use Sunhill\ORM\Tests\Unit\CommonStorage\DummyStoreStorage;
use Sunhill\ORM\Utils\ObjectDataGenerator;
use Sunhill\ORM\Facades\ObjectData;


/**
 * @group loadobject
 * @group load
 * @author klaus
 */
class StoreTest extends TestCase
{
    
    public function testDummyStore()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(Dummy::class);
        
        $storage = new TestStorage();
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        ObjectData::shouldReceive('getUUID')->once()->andReturn();
        ObjectData::shouldReceive('getDBTime')->twice()->andReturn('2023-06-26 11:32:29');
        $test = new Dummy();
        $test->dummyint = 3333;
        
        $test->commit();

        $compare = new DummyStoreStorage();
        $compare->assertStorageEquals($storage);
    }
        
}