<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;

class TestParentLoadStorage extends TestStorage
{
    
    protected function setObjectValues()
    {
        $this->setValue('_created_at','2019-05-15 10:00:00');
        $this->setValue('_updated_at','2019-05-15 10:00:00');
        $this->setValue('_uuid','i123');
        $this->setValue('_owner',0);
        $this->setValue('_group',0);
        $this->setValue('_read',7);
        $this->setValue('_edit',7);
        $this->setValue('_delete',7);
    }
    
    public function __construct()
    {
        $this->setObjectValues();
        
        $this->setValue('parentint',111);
        $this->setValue('parentchar','ABC');
        $this->setValue('parentfloat',1.11);
        $this->setValue('parenttext','Lorem ipsum');
        $this->setValue('parentdatetime','1974-09-15 17:45:00');
        $this->setValue('parentdate','1974-09-15');
        $this->setValue('parenttime','17:45:00');
        $this->setValue('parentenum','testC');
        $this->setValue('parentobject',1);
        $this->setValue('parentcalc','111A');
        $this->setValue('parentcollection',7);        
        $this->setValue('tags',[3,4,5]);
        $this->setValue('parentsarray',['String A','String B']);
        $this->setValue('parentoarray',[2,3]);
        $this->setValue('parentmap',['KeyA'=>'Value A','KeyB'=>'Value B']);        
        
        $entry1 = new \StdClass();
        $entry1->allowed_classes = '|testparent|';
        $entry1->name = 'attribute1';
        $entry1->attribute_id = 2;
        $entry1->type = 'integer';
        $entry1->value = 123;
        
        $entry2 = new \StdClass();
        $entry2->allowed_classes = '|testparent|';
        $entry2->name = 'attribute2';
        $entry2->attribute_id = 3;
        $entry2->type = 'integer';
        $entry2->value = 222;
                
        $this->setValue('attributes',[$entry1, $entry2]);
    }
    
}