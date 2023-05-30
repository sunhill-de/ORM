<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Query\ArrayQuery;
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Query\ConditionBuilder;

class TestQuery extends ArrayQuery
{

    protected $allowed_order_keys = ['none','name','value','payload'];
    
    protected function entry($name, $value, $payload)
    {
        $result = new \StdClass();
        $result->name = $name;
        $result->value = $value;
        $result->payload = $payload;
        return $result;
    }
    
    protected function getRawData()
    {
        return [
            $this->entry('ABC',123,'ZZZ'),
            $this->entry('DEF',234,'XXX'),
            $this->entry('GHI',345,'YYY')
        ];
    }
    
}

class ArrayQueryTest extends TestCase
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
            $test = new TestQuery();
            
            $this->assertEquals(3, $test->count());
        }
        
        public function testFirst()
        {
            $test = new TestQuery();
            
            $this->assertDataEquals(['name'=>'ABC','value'=>123,'payload'=>'ZZZ'], $test->first());
        }
        
        public function testGet()
        {
            $test = new TestQuery();
            
            $this->assertArrayEquals([
                ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
                ['name'=>'DEF','value'=>234,'payload'=>'XXX'],
                ['name'=>'GHI','value'=>345,'payload'=>'YYY'],               
            ], $test->get());
            
        }
        
        public function testGetWithOrder()
        {
            $test = new TestQuery();
            
            $this->assertArrayEquals([
                ['name'=>'DEF','value'=>234,'payload'=>'XXX'],
                ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
                ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
            ], $test->orderBy('payload')->get());            
        }
        
        public function testGetWithWhere()
        {
            $test = new TestQuery();
            
            $this->assertArrayEquals([
                ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
            ], $test->where('name','GHI')->get());            
        }
        
        public function testGetWithMoreWheres()
        {   $test = new TestQuery();
            
            $this->assertArrayEquals([
                ['name'=>'DEF','value'=>234,'payload'=>'XXX'],
            ], $test->where('name','>','ABC')->where('value','<',345)->get());        
        }
        
        public function testGetWithOrWhere()
        {   $test = new TestQuery();
        
            $this->assertArrayEquals([
                ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
                ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
            ], $test->where('name','=','ABC')->orWhere('value','=',345)->get());
        }
        
        public function testGetWithOrWhereSubquery()
        {   $test = new TestQuery();
        
            $this->assertArrayEquals([
                ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
                ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
            ], $test->where('name','=','ABC')->orWhere(function(ConditionBuilder $query) {
                $query->where('value','<',999)->where('name','>','DEF');
            })->get());
        }
        
}