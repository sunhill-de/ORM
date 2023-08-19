<?php

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Query\DBQuery;
use Sunhill\ORM\Query\ConditionBuilder;
use Sunhill\ORM\Query\QueryException;
use Sunhill\ORM\Query\UnknownFieldException;
use Sunhill\ORM\Query\NotAllowedRelationException;

class TestDBQuery extends DBQuery
{

    protected $keys = [
        'id'=>'handleNumericField',
        'parentint'=>'handleNumericField',
        'parentchar'=>'handleStringField',
        'parentfloat'=>'handleNumericField',
        'parenttext'=>'handleStringField',
        'parentdatetime'=>'handleDateTimeField',
        'parentdate'=>'handleDateTimeField',
        'parenttime'=>'handleDateTimeField',
        'parentenum'=>'handleStringField',
        'parentobject'=>'handleNumericField',
        'parentcalc'=>'handleStringField'
    ];

    protected function handleSpecial($key, $relation, $value)
    {
        $subquery = DB::table('objects')->where('class',$value);
        $this->query->whereIn('parentobject',$subquery);    
    }
    
    protected function getBasicTable()
    {
        return DB::table('testparents');
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
        
        /**
         * @dataProvider QueryProvider
         */
        public function testQuery($modifier, $data_callback, $expect)
        {
            if (is_a($expect, QueryException::class, true)) {
                $this->expectException($expect);
            }
            $test = new TestDBQuery();
            $result = $modifier($test);
            if (!is_null($data_callback)) {
                $result = $data_callback($result);
            }
            $this->assertEquals($expect, $result);            
        }
        
        public static function QueryProvider()
        {
            return [
                [   // Test simple count
                    function($query) { return $query->count(); }, 
                    null, 
                    18
                ],
                [   // Test first()
                    function($query){ return $query->first(); }, 
                    function($result) { return $result->id; }, 
                    9
                ],
                [   // Test get()
                    function($query){ return $query->get(); }, 
                    function($result) { return $result[0]->id; }, 
                    9 
                ],
                [   // Test orderBy()
                    function($query){ return $query->orderBy('parentchar','desc')->first(); },
                    function($result) { return $result->id; },
                    19
                ],
                [   // Test where()
                    function($query){ return $query->where('parentchar','EEE')->first(); },
                    function($result) { return $result->id; },
                    12
                ],                    
                [   // Test where()
                    function($query){ return $query->where('parentchar','DEF')->count(); },
                    function($result) { return $result; },
                    5
                ],
                [  // Test more where()
                    function($query){ return $query->where('parentchar','DEF')->where('parentint','<',500)->count(); },
                    function($result) { return $result; },
                    2                    
                ],
                [  // Test orWhere()
                    function($query){ return $query->where('parentchar','ABC')->orWhere('parentchar','=','DEF')->count(); },
                    function($result) { return $result; },
                    6
                ],
                [  // Test orWhere with subquery
                    function($query){ return $query->where('parentchar','ABC')->orWhere(function($query) { return $query->where('parentchar','DEF')->where('parentint','<',500); })->count(); },
                    function($result) { return $result; },
                    3                    
                ],
                [  // Test limit and offset
                        function($query){ return $query->offset(17)->limit(1)->first(); },
                        function($result) { return $result->id; },
                        26
                ],
                [ // Unknown field
                    function($query){ return $query->where('nonexisting','ABC'); },
                    null,
                    UnknownFieldException::class,
                ],
                [
                    function($query){ return $query->where('parentchar','?!','ABC'); },
                    null,
                    NotAllowedRelationException::class,                    
                ],
                [
                    function($query){ return $query->where('parentchar','begins with','D')->first(); },
                    function($result) { return $result->id; },
                    10                    
                ],                
                [
                    function($query){ return $query->where('parentchar','end with','I')->first(); },
                    function($result) { return $result->id; },
                    11
                ],
                [
                    function($query){ return $query->where('parentchar','contains','Z')->first(); },
                    function($result) { return $result->id; },
                    15
                ],
                [
                    function($query){ return $query->where('parentchar','in',['DEF','ABC'])->count(); },
                    function($result) { return $result; },
                    6                    
                ],
                [
                    function($query){ 
                        $test = collect(['DEF','ABC']);
                        return $query->where('parentchar','in',$test)->count(); 
                    },
                    function($result) { return $result; },
                    6                    
                ]
            ];    
        }
        
 }