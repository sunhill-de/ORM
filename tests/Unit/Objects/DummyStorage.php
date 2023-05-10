<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;

class DummyStorage extends StorageBase
{
    
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
     
     protected function fillValues()
     {
         $this->fillCommon();
         switch ($this->getCaller()::class) {
             case Dummy::class:
                 $this->fillDummy();
                 break;
             case TestParent::class:
                 $this->fillTestParent();
                 break;
         }
     }
     
     protected function doLoad(int $id)
     {
        $this->fillValues();         
     }
     
     protected function doStore(): int
     {
         
     }
     
     protected function doUpdate(int $id)
     {
         
     }
     
     protected function doDelete(int $id)
     {
         
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