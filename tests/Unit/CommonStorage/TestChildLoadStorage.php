<?php

namespace Sunhill\ORM\Tests\Unit\CommonStorage;

use Sunhill\ORM\Tests\Utils\TestStorage;

class TestChildLoadStorage extends TestStorage
{
    
    protected function setObjectValues()
    {
        $this->setValue('_created_at','2019-05-15 10:00:00');
        $this->setValue('_updated_at','2019-05-15 10:00:00');
        $this->setValue('_uuid','r123');
        $this->setValue('_owner',0);
        $this->setValue('_group',0);
        $this->setValue('_read',7);
        $this->setValue('_edit',7);
        $this->setValue('_delete',7);
    }
    
    public function __construct()
    {
        $this->setObjectValues();
                
        $this->setValue('parentint',123);
        $this->setValue('parentchar','RRR');
        $this->setValue('parentfloat',1.23);
        $this->setValue('parenttext','Lorem ipsum dolo');
        $this->setValue('parentdatetime','1978-06-05 11:45:00');
        $this->setValue('parentdate','1978-06-05');
        $this->setValue('parenttime','11:45:00');
        $this->setValue('parentenum','testC');
        $this->setValue('parentobject',3);
        $this->setValue('parentcalc','123A');
        $this->setValue('parentcollection',4);        
        $this->setValue('childint',777);
        $this->setValue('childchar','WWW');
        $this->setValue('childfloat',1.23);
        $this->setValue('childtext','amet. Lorem ipsum dolo');
        $this->setValue('childdatetime','1978-06-05 11:45:00');
        $this->setValue('childdate','1978-06-05');
        $this->setValue('childtime','11:45:00');
        $this->setValue('childenum','testC');
        $this->setValue('childobject',3);
        $this->setValue('childcalc','777B');
        $this->setValue('childcollection',9);                
        $this->setValue('tags',[1,2,4]);        
        $this->setValue('parentsarray',['ABCDEFG','HIJKLMN']);
        $this->setValue('parentoarray',[4,5]);        
        $this->setValue('parentmap',['KeyA'=>'ABC','KeyC'=>'DEF']);        
        $this->setValue('childsarray',['OPQRSTU','VXYZABC']);
        $this->setValue('childoarray',[3,4,5]);
        $this->setValue('childmap',['KeyA'=>1,'KeyC'=>4]);
        
        $entry1 = new \StdClass();
        $entry1->allowed_classes = '|testparent|';
        $entry1->name = 'attribute1';
        $entry1->attribute_id = 2;
        $entry1->type = 'integer';
        $entry1->value = 654;
        
        $entry2 = new \StdClass();
        $entry2->allowed_classes = '|testparent|';
        $entry2->name = 'attribute2';
        $entry2->attribute_id = 3;
        $entry2->type = 'integer';
        $entry2->value = 543;
                
        $this->setValue('attributes',[$entry1, $entry2]);
    }
    
}