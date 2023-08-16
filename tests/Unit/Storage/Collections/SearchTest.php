<?php

/**
 * Note: To be excactly this is not a unit test because it depends on WhereParser. Due the fact that these both
 * build a structural unit and the latter was copied from the MysqlQuery code it will remain in the unit tests.
 */
namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Illuminate\Database\Eloquent\Collection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\CalcClass;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlQuery;

class SearchTest extends DatabaseTestCase
{
    
    /**
     * @dataProvider CollectionSearchProvider
     * @param unknown $class
     * @param unknown $modifier
     * @param unknown $expect
     * @group searchcollection
     * @group search
     * @group collection
     */
    
    public function testCollectionSearch($class, $modifier, $expect)
    {
        $collection = new $class();
        $test = new MysqlStorage();
        $test->setCollection($collection);
                
        $result = $modifier($test->dispatch('search'));
        
        if (is_a($result, \Illuminate\Support\Collection::class)) {
            $result = $result->map(function($item, int $key) {
               return $item->id; 
            })->toArray();
        } else if (is_a($result, \StdClass::class)) {
            $result = $result->id;
        }
        $this->assertEquals($expect, $result);
        
    }
    
    public static function CollectionSearchProvider()
    {
        return [
            [DummyCollection::class, function($query) { return $query->count(); }, 8],
            [DummyCollection::class, function($query) { return $query->get(); }, [1,2,3,4,5,6,7,8]],
            [DummyCollection::class, function($query) { return $query->first(); }, 1],
            
            [DummyCollection::class, function($query) { return $query->where('dummyint',123)->get(); }, [1,3,5]],
            [DummyCollection::class, function($query) { return $query->where('dummyint',0)->orWhere('dummyint',123)->get(); }, [1,3,5]],
            [DummyCollection::class, function($query) { return $query->where('dummyint',123)->count(); }, 3],
            [DummyCollection::class, function($query) { return $query->where('dummyint',123)->first(); }, 1],
            [DummyCollection::class, function($query) { return $query->where('dummyint',234)->get(); }, [2]],
            [DummyCollection::class, function($query) { return $query->where('dummyint',666)->get(); }, []],            
            
            [ComplexCollection::class, function($query) { return $query->where('field_int','<',123)->get(); }, [9]],
            [ComplexCollection::class, function($query) { return $query->whereNot('field_int','>=',123)->get(); }, [9]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','<',123)->orWhere('field_int','>',900)->get(); }, [9,25]],            
            [ComplexCollection::class, function($query) { return $query->where('field_int','<',123)->orWhereNot('field_int','<=',900)->get(); }, [9,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','<=',123)->get(); }, [9,10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','<>',123)->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','>',900)->get(); }, [25]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','>=',900)->get(); }, [19,25]],            
            
            [ComplexCollection::class, function($query) { return $query->where('field_int','in',[111,123])->get(); }, [9,10,12,17,26]],            
            [ComplexCollection::class, function($query) { return $query->whereIn('field_int',[111,123])->get(); }, [9,10,12,17,26]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_bool')->get(); }, [10,11,13,14,16,17,19,20,22,23,25,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_bool',true)->get(); }, [10,11,13,14,16,17,19,20,22,23,25,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_bool',false)->get(); }, [9,12,15,18,21,24]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_char','ABC')->get(); }, [9]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','DEF')->get(); }, [10,13,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','AAA')->get(); }, []],
            [ComplexCollection::class, function($query) { return $query->where('field_char',null)->get(); }, [16,24,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','in',['ABC','DEF'])->get(); }, [9,10,13,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','begins with','A')->get(); }, [9,23]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','ends with','T')->get(); }, [14,15]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','contains','Z')->get(); }, [15,19,20]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','<>','DEF')->get(); }, [9,11,12,14,15,17,19,20,22,23]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','!=','DEF')->get(); }, [9,11,12,14,15,17,19,20,22,23]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','<','DEF')->get(); }, [9,23]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','<=','DEF')->get(); }, [9,10,13,18,21,23,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','>','ZOO')->get(); }, [19]],
            [ComplexCollection::class, function($query) { return $query->where('field_char','>=','ZOO')->get(); }, [19,20]],

            [ComplexCollection::class, function($query) { return $query->where('field_float','=',1.23)->get(); }, [10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_float','<',1.23)->get(); }, [9]],
            [ComplexCollection::class, function($query) { return $query->where('field_float','<=',1.23)->get(); }, [9,10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_float','<>',1.23)->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_float','>',9.00)->get(); }, [25]],
            [ComplexCollection::class, function($query) { return $query->where('field_float','>=',9.00)->get(); }, [19,25]],

            [ComplexCollection::class, function($query) { return $query->where('field_datetime','=','1974-09-15 17:45:00')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','date','1974-09-15')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->whereDate('field_datetime','1974-09-15')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','Date','1974-09-15')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->whereTime('field_datetime','17:45:00')->get(); }, [9,15,16,18,19,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','time','17:45:00')->get(); }, [9,15,16,18,19,25]],
            [ComplexCollection::class, function($query) { return $query->whereDay('field_datetime','15')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','day','15')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereMonth('field_datetime','9')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','month','9')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereYear('field_datetime','1974')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','year','1974')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','<','1974-09-15 17:45:00')->get(); }, [10,11,19,20]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','<=','1974-09-15 17:45:00')->get(); }, [9,10,11,15,18,19,20,25]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_date','=','1974-09-15')->get(); }, [9,10,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->whereDay('field_date','15')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereMonth('field_date','9')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereYear('field_date','1974')->get(); }, [9,10,15,18,25]],

            [ComplexCollection::class, function($query) { return $query->where('field_time','=','17:45:00')->get(); }, [9,10,15,16,18,19,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_text','contains','dolor')->get(); }, [13,14,23,24,25,26]],
 
            
            [ComplexCollection::class, function($query) { return $query->where('field_enum','=','testA')->get(); }, [12]], 
            [ComplexCollection::class, function($query) { return $query->whereIn('field_enum',['testA','testB'])->get(); }, [10,12,15,18,22,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_enum','<>','testC')->get(); }, [10,12,15,18,22,26]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_object','=',5)->get(); }, [11,19]],
            [ComplexCollection::class, function($query) { 
                $dummy = new Dummy();
                $dummy->load(5);
                return $query->where('field_object','=',$dummy)->get(); 
            }, [11,19]],
            [ComplexCollection::class, function($query) { return $query->where('field_object','=',null)->get(); }, [13,14,15,16,21,22,23,24,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_object','<>',5)->get(); }, [9,10,12,17,18,20,25]],
            [ComplexCollection::class, function($query) { return $query->whereNull('field_object')->get(); }, [13,14,15,16,21,22,23,24,26]],
            [ComplexCollection::class, function($query) { return $query->whereIn('field_object',[5,1])->get(); }, [9,11,19]],
            [ComplexCollection::class, function($query) { 
                $dummy1 = new Dummy();
                $dummy1->load(5);
                $dummy2 = new Dummy();
                $dummy2->load(1);
                return $query->whereIn('field_object',[$dummy1,$dummy2])->get(); 
            }, [9,11,19]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','=',['DEFG'])->get(); }, [14]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','=',['String A','String B'])->get(); }, [9]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','<>',['DEFG'])->get(); }, [9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','=',['String A'])->get(); }, []],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','=',['String A','String B','String C'])->get(); }, []],
            
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','contains','DEFG')->get(); }, [10,14]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','all of',['ABCD','DEFG'])->get(); }, [10]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','any of',['HALLO','DEFG'])->get(); }, [10,14,19]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','none of',['HALLO','DEFG'])->get(); }, [9,11,12,13,15,16,17,18,20,21,22,23,24,25,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','empty')->get(); }, [12,15,16,20,21,23,24,25,26]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_calc','=',"123A")->get(); },[10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_calc','=',"111A")->get(); },[9]],
            [ComplexCollection::class, function($query) { return $query->where('field_calc','=',"5A")->get(); },[]],
            [ComplexCollection::class, function($query) { return $query->where('field_calc','<',"300A")->get(); },[9,10,11,12,13,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_calc','>',"800A")->get(); },[19,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_calc','<=',"123A")->get(); },[9,10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_calc','>=',"800A")->get(); },[18,19,25]],
            [ComplexCollection::class, function($query) { return $query->where("field_calc", "<>", "123A")->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [ComplexCollection::class, function($query) { return $query->where("field_calc", "!=", "123A")->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [ComplexCollection::class, function($query) { return $query->where("field_calc", "in", ["111A","123A"])->get(); },[9,10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where("field_calc", "begins with", "2")->get(); },[11,13]],
            [ComplexCollection::class, function($query) { return $query->where("field_calc", "ends with", "3A")->get(); },[10,12,17,24,26]],
            [ComplexCollection::class, function($query) { return $query->where("field_calc", "contains", "5")->get(); },[14,21,23]],
  
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","any of",[3])->get(); },[9,10,13,18]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","any of",[8])->get(); },[]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","any of",[8,3])->get(); },[9,10,13,18]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","any of",[7])->get(); },[11,19]],
            [ComplexCollection::class, function($query) { 
                $dummy1 = new Dummy();
                $dummy1->load(8);
                $dummy2 = new Dummy();
                $dummy2->load(3);
                return $query->where("field_oarray","any of",[$dummy1,$dummy2])->get(); 
            },[9,10,13,18]],
            
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","all of",[1,2])->get(); },[10,13,18]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","all of",[1,7])->get(); },[]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","none of",[1,2])->get(); },[12,15,16,17,20,21,22,23,24,25,26]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","none of",[1,7])->get(); },[9,12,15,16,17,20,21,22,23,24,25,26]],
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","empty")->get(); },[12,15,16,20,21,23,24,25,26]],
            
            [ComplexCollection::class, function($query) { return $query->where("field_smap","any of",['ABCD','CC'])->get(); },[10,11,13]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","any of",['ZYC'])->get(); },[]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","all of",['ABCD','XYZA'])->get(); },[13]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","all of",['ABCD','HOLA'])->get(); },[]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","none of",['ABCD','AA'])->get(); },[9,12,14,15,16,17,18,19,20,21,22,23,24,25,26]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","empty")->get(); },[12,15,16,20,21,23,24,25,26]],            
            [ComplexCollection::class, function($query) { return $query->where("field_smap","any key of",['KeyA','KeyC'])->get(); },[9,10,11,13,14,17,18,19,22]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","all keys of",['KeyA','KeyB','KeyC'])->get(); },[10,11,13,18,19,22]],
            [ComplexCollection::class, function($query) { return $query->where("field_smap","none key of",['KeyB','KeyC'])->get(); },[12,14,15,16,20,21,23,24,25,26]],
            
           ];
    }
}