<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;

class StorageTest extends sunhill_testcase_db
{

    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_special_table('passthrus');
        $this->create_special_table('testparents');
        $this->create_special_table('testchildren');
        $this->create_special_table('referenceonlies');        
    }
    
    protected function prepare_read() {
        $this->prepare_tables();
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [[1,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [2,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [3,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [4,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [5,"\\Sunhill\\Test\\ts_testparent",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [6,"\\Sunhill\\Test\\ts_testchild",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [7,"\\Sunhill\\Test\\ts_referenceonly",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
         ]);
        $this->insert_into('dummies',['id','dummyint'],[[1,123],[2,234],[3,345],[4,456]]);
        $this->insert_into('tags',['id','created_at','updated_at','name','options','parent_id'],
            [
                [1,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagA',0,0],
                [2,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagB',0,0],
                [3,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagC',0,2],
                [4,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagD',0,0],
                [5,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagE',0,0],
                [6,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagF',0,0],
            ]);
        $this->insert_into('tagcache',['id','name','tag_id','created_at','updated_at'],
            [
                [1,'TagA',1,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,'TagB',2,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,'TagC',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,'TagC.TagB',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [5,'TagD',4,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,'TagE',5,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,'TagF',6,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('tagobjectassigns',['container_id','tag_id'],
            [
                [1,1]
            ]);
    }
    
    /**
     * 
     * @param unknown $id
     * @param unknown $fieldname
     * @param unknown $expected
     * @dataProvider SimpleProvider
     */
    public function testSimple($id,$class,$fieldname,$expected) {
        $this->prepare_read();
        $object = new $class();
        $loader = new \Sunhill\Storage\storage_load($object);
        $loader->set_inheritance($object->get_inheritance(true));
        $loader->load_object($id);
        $this->assertEquals($expected,$loader->$fieldname);
    }
    
    public function SimpleProvider() {
        return [
            [1,'Sunhill\\Test\\ts_dummy','dummyint',123],
            [2,'Sunhill\\Test\\ts_dummy','dummyint',234],
            [1,'Sunhill\\Test\\ts_dummy','tags',['TagA']]
        ];
    }
}
