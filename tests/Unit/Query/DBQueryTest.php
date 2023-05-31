<?php

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Query\DBQuery;
use Sunhill\ORM\Query\ConditionBuilder;

class TestDBQuery extends DBQuery
{

    protected function getBasicTable()
    {
        return DB::table('tags');
    }
    
}

class DBQueryTest extends DatabaseTestCase
{
 
        protected function assertDataEquals($assertion, $data)
        {
            foreach ($assertion as $key => $value) {
                if ($data->$key !== $value) {
                    $this->assertTrue(false, $data->$key." is not asserted ".$value);
                }
            }
            $this->assertTrue(true);
        }
        
        protected function assertArrayEquals($assertion, $data)
        {
            $data = array_values($data->toArray());
            if (count($assertion) !== count($data)) {
                $this->assertTrue(false, "The data count ".count($data)." doesn't match expected ".count($assertion));
                return;
            }
            for ($i=0;$i<count($assertion);$i++) {
                $this->assertDataEquals($assertion[$i], $data[$i]);
            }
        }
        
        public function testCount()
        {
            $test = new TestDBQuery();
            
            $this->assertEquals(9, $test->count());
        }
        
        public function testFirst()
        {
            $test = new TestDBQuery();
            
            $this->assertDataEquals(['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0], $test->first());
        }
        
        public function testGet()
        {
            $test = new TestDBQuery();
            
            $this->assertArrayEquals([
                ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
                ['id'=>2,'name'=>'TagB','parent_id'=>0,'options'=>0],
                ['id'=>3,'name'=>'TagC','parent_id'=>2,'options'=>0],
                ['id'=>4,'name'=>'TagD','parent_id'=>0,'options'=>0],
                ['id'=>5,'name'=>'TagE','parent_id'=>0,'options'=>0],
                ['id'=>6,'name'=>'TagF','parent_id'=>0,'options'=>0],
                ['id'=>7,'name'=>'TagG','parent_id'=>6,'options'=>0],
                ['id'=>8,'name'=>'TagE','parent_id'=>7,'options'=>0],
                ['id'=>9,'name'=>'TagZ','parent_id'=>0,'options'=>0],
            ], $test->get());
            
        }
        
        public function testGetWithOrder()
        {
            $test = new TestDBQuery();
            
            $this->assertArrayEquals([
                ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
                ['id'=>2,'name'=>'TagB','parent_id'=>0,'options'=>0],
                ['id'=>3,'name'=>'TagC','parent_id'=>2,'options'=>0],
                ['id'=>4,'name'=>'TagD','parent_id'=>0,'options'=>0],
                ['id'=>5,'name'=>'TagE','parent_id'=>0,'options'=>0],
                ['id'=>8,'name'=>'TagE','parent_id'=>7,'options'=>0],
                ['id'=>6,'name'=>'TagF','parent_id'=>0,'options'=>0],
                ['id'=>7,'name'=>'TagG','parent_id'=>6,'options'=>0],
                ['id'=>9,'name'=>'TagZ','parent_id'=>0,'options'=>0],
            ], $test->orderBy('name')->get());            
        }
        
        public function testGetWithWhere()
        {
            $test = new TestDBQuery();
            
            $this->assertArrayEquals([
                ['id'=>3,'name'=>'TagC','parent_id'=>2,'options'=>0],
                ['id'=>7,'name'=>'TagG','parent_id'=>6,'options'=>0],
                ['id'=>8,'name'=>'TagE','parent_id'=>7,'options'=>0],
            ], $test->where('parent_id','<>',0)->get());            
        }
        
        public function testGetWithMoreWheres()
        {   
            $test = new TestDBQuery();
            
            $this->assertArrayEquals([
                ['id'=>7,'name'=>'TagG','parent_id'=>6,'options'=>0],
                ['id'=>8,'name'=>'TagE','parent_id'=>7,'options'=>0],
            ], $test->where('parent_id','<>',0)->where('id','>',3)->get());
        }
        
        public function testGetWithOrWhere()
        {   $test = new TestDBQuery();
        
            $result = $test->where('parent_id',0)->orWhere('name','TagC')->orderBy('id')->get();
        
            $this->assertArrayEquals([
                ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
                ['id'=>2,'name'=>'TagB','parent_id'=>0,'options'=>0],
                ['id'=>3,'name'=>'TagC','parent_id'=>2,'options'=>0],
                ['id'=>4,'name'=>'TagD','parent_id'=>0,'options'=>0],
                ['id'=>5,'name'=>'TagE','parent_id'=>0,'options'=>0],
                ['id'=>6,'name'=>'TagF','parent_id'=>0,'options'=>0],
                ['id'=>9,'name'=>'TagZ','parent_id'=>0,'options'=>0],
            ], $result);
        }
        
        public function testGetWithOrWhereSubquery()
        {   $test = new TestDBQuery();
        
            $this->assertArrayEquals([
                ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
                ['id'=>2,'name'=>'TagB','parent_id'=>0,'options'=>0],
                ['id'=>4,'name'=>'TagD','parent_id'=>0,'options'=>0],
                ['id'=>5,'name'=>'TagE','parent_id'=>0,'options'=>0],
                ['id'=>6,'name'=>'TagF','parent_id'=>0,'options'=>0],
                ['id'=>8,'name'=>'TagE','parent_id'=>7,'options'=>0],
                ['id'=>9,'name'=>'TagZ','parent_id'=>0,'options'=>0],
            ], $test->where('parent_id','=',0)->orWhere(function($query) {
                $query->where('parent_id','<>',0)->where('id','>',7);
            })->orderby('id')->get());
        }
        
}