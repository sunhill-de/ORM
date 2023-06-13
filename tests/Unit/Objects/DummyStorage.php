<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyCalculated;

class DummyStorage extends StorageBase
{
    
     public $state = 'none';
     
     public function __construct(public $type = Dummy::class) {}
     
     public function setType(string $type)
     {
        $this->type = $type;    
     }
     
     protected function fillCommon()
     {
         $this->setEntity('uuid','123a');
         $this->setEntity('obj_owner',0);
         $this->setEntity('obj_group',0);
         $this->setEntity('obj_read',7);
         $this->setEntity('obj_edit',7);
         $this->setEntity('obj_delete',7);
         $this->setEntity('created_at','2023-05-05 10:00:00');
         $this->setEntity('modified_at','2023-05-05 10:00:00');
     }
     
     protected function fillDummy()
     {
         $this->setEntity('classname','dummy');
         $this->setEntity('dummyint', 123);
         $this->setEntity('tags', [1,2,3]);
         $attribute = new \StdClass();
         $attribute->allowed_objects = 'object';
         $attribute->attribute_id = 4;
         $attribute->name = 'general_attribute';
         $attribute->type = 'integer';
         $attribute->value = 444;
         $this->setEntity('attributes',[$attribute]);
     }
     
     protected function fillTestParent()
     {
        $this->setEntity('parentchar','ABC');
        $this->setEntity('parentint',123);
        $this->setEntity('parentfloat',1.23);
        $this->setEntity('parenttext','Lorem ipsum');
        $this->setEntity('parentdatetime','2023-05-10 11:43:00');
        $this->setEntity('parentdate','2023-05-10');
        $this->setEntity('parenttime','11:43:00');
        $this->setEntity('parentenum','testC');
        $this->setEntity('parentobject',1);
        $this->setEntity('parentsarray',['AAA','BBB','CCC']);
        $this->setEntity('parentoarray',[2,3,4]);
        $this->setEntity('parentcalc','123A');
        $this->setEntity('nosearch',2);
     }
     
     protected function fillDummyCollection()
     {
        $this->setEntity('dummyint',123, 'dummycollection', PropertyInteger::class);    
     }
     
     protected function fillComplexCollection()
     {
         $this->setEntity('field_char','ABC','complexcollections',PropertyVarchar::class);
         $this->setEntity('field_int',123,'complexcollections',PropertyInteger::class);
         $this->setEntity('field_float',1.23,'complexcollections',PropertyFloat::class);
         $this->setEntity('field_text','Lorem ipsum','complexcollections',PropertyText::class);
         $this->setEntity('field_datetime','2023-05-10 11:43:00','complexcollections',PropertyDatetime::class);
         $this->setEntity('field_date','2023-05-10','complexcollections',PropertyDate::class);
         $this->setEntity('field_time','11:43:00','complexcollections',PropertyTime::class);
         $this->setEntity('field_enum','testC','complexcollections',PropertyEnum::class);
         $this->setEntity('field_object',1,'complexcollections',PropertyObject::class);
         $this->setEntity('field_sarray',['AAA','BBB','CCC'],'complexcollections',PropertyArray::class);
         $this->setEntity('field_oarray',[2,3,4],'complexcollections',PropertyArray::class);
         $this->setEntity('field_smap',['KeyA'=>'ValueA','KeyB'=>'ValueB'],'complexcollections',PropertyMap::class);
         $this->setEntity('field_calc','123A','complexcollections',PropertyCalculated::class);
     }
     
     protected function fillEmptyCollection()
     {
         $this->setEntity('field_char','ABC','complexcollections',PropertyVarchar::class);
         $this->setEntity('field_int',null,'complexcollections',PropertyInteger::class);
         $this->setEntity('field_float',1.23,'complexcollections',PropertyFloat::class);
         $this->setEntity('field_text','Lorem ipsum','complexcollections',PropertyText::class);
         $this->setEntity('field_datetime','2023-05-10 11:43:00','complexcollections',PropertyDatetime::class);
         $this->setEntity('field_date','2023-05-10','complexcollections',PropertyDate::class);
         $this->setEntity('field_time','11:43:00','complexcollections',PropertyTime::class);
         $this->setEntity('field_enum','testC','complexcollections',PropertyEnum::class);
         $this->setEntity('field_object',null,'complexcollections',PropertyObject::class);
         $this->setEntity('field_sarray',null,'complexcollections',PropertyArray::class);
         $this->setEntity('field_oarray',null,'complexcollections',PropertyArray::class);
         $this->setEntity('field_smap',null,'complexcollections',PropertyMap::class);
         $this->setEntity('field_calc','123A','complexcollections',PropertyCalculated::class);
     }
     
     protected function fillValues()
     {
         switch ($this->type) {
             case Dummy::class:
                 $this->fillCommon();
                 $this->fillDummy();
                 break;
             case TestParent::class:
                 $this->fillCommon();
                 $this->fillTestParent();
                 break;
             case DummyCollection::class:
                 $this->fillDummyCollection();
                 break;
             case ComplexCollection::class:
                 $this->fillComplexCollection();
                 break;
             case ComplexCollection::class.'empty':
                 $this->fillEmptyCollection();
                 break;
         }
     }
     
     protected function doLoad(int $id)
     {
        $this->fillValues();
        $this->state = 'loaded';
     }
     
     protected function doStore(): int
     {
         $this->setEntity('uuid','abcdefghi');
         $this->setEntity('created_at','2023-05-13 19:30:20');
         $this->setEntity('updated_at','2023-05-13 19:30:20');
         $this->state = 'stored';
         return 1;
     }
     
     protected function doUpdate(int $id)
     {
         $this->state = 'updated';
         
     }
     
     protected function doDelete(int $id)
     {
         $this->state = 'deleted';
         
     }
     
     protected function doMigrate()
     {
         
     }
     
     protected function doPromote()
     {
         
     }
     
     protected function doDegrade()
     {
         
     }
     
     protected function doSearch()
     {
         
     }
     
     protected function doDrop()
     {
         
     }
    
}