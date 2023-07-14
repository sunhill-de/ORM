<?php

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
     /**       [DummyCollection::class, function($query) { return $query->count(); }, 8],
            [DummyCollection::class, function($query) { return $query->get(); }, [1,2,3,4,5,6,7,8]],
            [DummyCollection::class, function($query) { return $query->first(); }, 1],
            [DummyCollection::class, function($query) { return $query->where('dummyint',123)->get(); }, [1,3,5]],
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
            [ComplexCollection::class, function($query) { return $query->whereTime('field_datetime','17:45:00')->get(); }, [9,15,16,18,19,25]],
            [ComplexCollection::class, function($query) { return $query->whereDay('field_datetime','15')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereMonth('field_datetime','9')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereYear('field_datetime','1974')->get(); }, [9,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','<','1974-09-15 17:45:00')->get(); }, [10,11,19,20]],
            [ComplexCollection::class, function($query) { return $query->where('field_datetime','<=','1974-09-15 17:45:00')->get(); }, [9,10,11,15,18,19,20,25]],
            
            [ComplexCollection::class, function($query) { return $query->where('field_date','=','1974-09-15')->get(); }, [9,10,15,18,25]],
            [ComplexCollection::class, function($query) { return $query->whereDay('field_date','15')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereMonth('field_date','9')->get(); }, [9,10,15,18,21,25]],
            [ComplexCollection::class, function($query) { return $query->whereYear('field_date','1974')->get(); }, [9,10,15,18,25]],

            [ComplexCollection::class, function($query) { return $query->where('field_time','=','17:45:00')->get(); }, [9,10,15,16,18,19,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_text','contains','dolor')->get(); }, [13,14,23,24,25,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_enum','=','testA')->get(); }, [12]], 
            
            [ComplexCollection::class, function($query) { return $query->where('field_object','=',5)->get(); }, [11,19]],
            [ComplexCollection::class, function($query) { 
                $dummy = new Dummy();
                $dummy->load(5);
                return $query->where('field_object','=',$dummy)->get(); 
            }, [11,19]],
            [ComplexCollection::class, function($query) { return $query->where('field_object','=',null)->get(); }, [13,14,15,16,21,22,23,24,26]],
            [ComplexCollection::class, function($query) { return $query->whereNull('field_object')->get(); }, [13,14,15,16,21,22,23,24,26]],
                        
            [ComplexCollection::class, function($query) { return $query->where('field_sarray','=',['DEFG'])->get(); }, [14]],
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
            [ComplexCollection::class, function($query) { return $query->where("field_oarray","any of",[7])->get(); },[11,19]],
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
            
            [CalcClass::class, function($query) { return $query->get(); }, [31]],
            [TestSimpleChild::class, function($query) { return $query->get(); }, [25,26]],
            [ThirdLevelChild::class, function($query) { return $query->get(); }, [33]],
            [Dummy::class, function($query) { return $query->where('dummyint','>',500)->orderBy('dummyint','desc')->get(); },[8,7,6]],
            [Dummy::class, function($query) { return $query->orderBy('dummyint')->get(); },[1,3,5,2,4,6,7,8]],
            [TestParent::class, function($query) { return $query->where('parentint','<',600)->where('parentchar','<','EEE')->orderBy('parentchar')->get(); },[9,23,10,13,21]],
            [TestParent::class, function($query) { return $query->offset(2)->limit(2)->get(); },[11,12]],
            [CalcClass::class, function($query) { return $query->count(); }, 1],
            [TestParent::class, function($query) { return $query->where('parentchar','=','DEF')->count(); },5],
            
            
            [TestParent::class, function($query) { return $query->where('parentint','=',123)->get(); },[10,12,17,26]],
            [TestParent::class, function($query) { return $query->where('parentint','=',111)->get(); },[9]],
            [TestParent::class, function($query) { return $query->where('parentint','=',5)->get(); },[]],
            [TestParent::class, function($query) { return $query->where('parentint','<',234)->get(); },[9,10,11,12,17,26]],
            [TestParent::class, function($query) { return $query->where('parentint','<=',234)->get(); },[9,10,11,12,13,17,26]],
            [TestParent::class, function($query) { return $query->where('parentint','>',800)->get(); },[19,25]],
            [TestParent::class, function($query) { return $query->where('parentint','>=',800)->get(); },[18,19,25]],
            [TestParent::class, function($query) { return $query->where("parentint", "<>", 123)->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class, function($query) { return $query->where("parentint", "in", [111,123])->get(); },[9,10,12,17,26]],
            
            [TestChild::class, function($query) { return $query->where( 'parentint','=',123)->get(); },[17]],
            [TestChild::class, function($query) { return $query->where( 'parentint','=',5)->get(); },[]],
            [TestChild::class, function($query) { return $query->where( 'parentint','<',400)->get(); },[17,23]],
            [TestChild::class, function($query) { return $query->where( 'parentint','>',700)->get(); },[18,19,24]],
            [TestChild::class, function($query) { return $query->where( "parentint", "!=", 123)->get(); }, [18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "parentint", "<>", 123)->get(); }, [18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "parentint", "in", [800,123])->get(); },[17,18]],
            [TestChild::class, function($query) { return $query->where( 'childint','=',801)->get(); },[18]],
            [TestChild::class, function($query) { return $query->where( 'childint','=',5)->get(); },[]],
            [TestChild::class, function($query) { return $query->where( 'childint','<',350)->get(); },[21,22,23]],
            [TestChild::class, function($query) { return $query->where( 'childint','>',800)->get(); },[18,19]],
            [TestChild::class, function($query) { return $query->where( "childint", "<>", 112)->get(); }, [17,18,19,20,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "childint", "in", [112,321])->get(); },[21,22]],
            // Test of float fields
            [TestParent::class, function($query) { return $query->where('parentfloat','=',1.23)->get(); },[10,12,17,26]],
            [TestParent::class, function($query) { return $query->where('parentfloat','=',1.11)->get(); },[9]],
            [TestParent::class, function($query) { return $query->where('parentfloat','=',5)->get(); },[]],
            [TestParent::class, function($query) { return $query->where('parentfloat','<',2.34)->get(); },[9,10,11,12,17,26]],
            [TestParent::class, function($query) { return $query->where('parentfloat','<=',2.34)->get(); },[9,10,11,12,13,17,26]],
            [TestParent::class, function($query) { return $query->where('parentfloat','>',8)->get(); },[19,25]],
            [TestParent::class, function($query) { return $query->where('parentfloat','>=',8)->get(); },[18,19,25]],
            [TestParent::class, function($query) { return $query->where("parentfloat", "<>", 1.23)->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class, function($query) { return $query->where("parentfloat", "in", [1.11,1.23])->get(); },[9,10,12,17,26]],
            [TestChild::class, function($query) { return $query->where( 'parentfloat','=',1.23)->get(); },[17]],
            [TestChild::class, function($query) { return $query->where( 'parentfloat','=',5)->get(); },[]],
            [TestChild::class, function($query) { return $query->where( 'parentfloat','<',4)->get(); },[17,23]],
            [TestChild::class, function($query) { return $query->where( 'parentfloat','>',7)->get(); },[18,19,24]],
            [TestChild::class, function($query) { return $query->where( "parentfloat", "<>", 1.23)->get(); }, [18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "parentfloat", "in", [8,1.23])->get(); },[17,18]],
            [TestChild::class, function($query) { return $query->where( 'childfloat','=',1.23)->get(); },[17]],
            [TestChild::class, function($query) { return $query->where( 'childfloat','=',5)->get(); },[]],
            [TestChild::class, function($query) { return $query->where( 'childfloat','<',3.50)->get(); },[17,23]],
            [TestChild::class, function($query) { return $query->where( 'childfloat','>',7)->get(); },[18,19,24]],
            [TestChild::class, function($query) { return $query->where( "childfloat", "<>", 1.23)->get(); }, [18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "childfloat", "in", [1.23,6.66])->get(); },[17,20]],
            // Test of varchar fields
            [TestParent::class, function($query) { return $query->where('parentchar','=',"DEF")->get(); },[10,13,18,21,25]],
            [TestParent::class, function($query) { return $query->where('parentchar','=',"EEE")->get(); },[12]],
            [TestParent::class, function($query) { return $query->where('parentchar','=',"WWW")->get(); },[]],
            [TestParent::class, function($query) { return $query->where('parentchar','<',"DEF")->get(); },[9,23]],
            [TestParent::class, function($query) { return $query->where('parentchar','<=',"DEF")->get(); },[9,10,13,18,21,23,25]],
            [TestParent::class, function($query) { return $query->where('parentchar','>',"XZT")->get(); },[19,20]],
            [TestParent::class, function($query) { return $query->where('parentchar','>=',"XZT")->get(); },[15,19,20]],
            [TestParent::class, function($query) { return $query->where("parentchar", "<>", "DEF")->get(); }, [9,11,12,14,15,17,19,20,22,23]],
            [TestParent::class, function($query) { return $query->where("parentchar", "!=", "DEF")->get(); }, [9,11,12,14,15,17,19,20,22,23]],
            [TestParent::class, function($query) { return $query->where("parentchar", "in", ["DEF","ABC"])->get(); },[9,10,13,18,21,25]],
            [TestParent::class, function($query) { return $query->where("parentchar", "begins with", "A")->get(); },[9,23]],
            [TestParent::class, function($query) { return $query->where("parentchar", "begins with", "AB")->get(); },[9]],
            [TestParent::class, function($query) { return $query->where("parentchar", "begins with", "K")->get(); },[]],
            [TestParent::class, function($query) { return $query->where("parentchar", "ends with", "T")->get(); },[14,15]],
            [TestParent::class, function($query) { return $query->where("parentchar", "ends with", "C")->get(); },[9]],
            [TestParent::class, function($query) { return $query->where("parentchar", "ends with", "K")->get(); },[]],
            [TestParent::class, function($query) { return $query->where("parentchar", "contains", "B")->get(); },[9]],
            [TestParent::class, function($query) { return $query->where("parentchar", "contains", "R")->get(); },[17,22,23]],
            [TestParent::class, function($query) { return $query->where("parentchar", "contains", "K")->get(); },[]],
            [TestChild::class, function($query) { return $query->where('parentchar','=',"DEF")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where('parentchar','=',"ZZZ")->get(); },[19]],
            [TestChild::class, function($query) { return $query->where('parentchar','=',"WWW")->get(); },[]],
            [TestChild::class, function($query) { return $query->where('parentchar','<',"CCC")->get(); },[23]],
            [TestChild::class, function($query) { return $query->where('parentchar','>',"YYY")->get(); },[19,20]],
            [TestChild::class, function($query) { return $query->where("parentchar", "<>", "DEF")->get(); }, [17,19,20,22,23]],
            [TestChild::class, function($query) { return $query->where("parentchar", "in", ["DEF","ABC"])->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where("parentchar", "begins with", "D")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where("parentchar", "begins with", "A")->get(); },[23]],
            [TestChild::class, function($query) { return $query->where("parentchar", "begins with", "K")->get(); },[]],
            [TestChild::class, function($query) { return $query->where("parentchar", "ends with", "F")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where("parentchar", "ends with", "O")->get(); },[20]],
            [TestChild::class, function($query) { return $query->where("parentchar", "ends with", "K")->get(); },[]],
            [TestChild::class, function($query) { return $query->where("parentchar", "contains", "O")->get(); },[20]],
            [TestChild::class, function($query) { return $query->where("parentchar", "contains", "R")->get(); },[17,22,23]],
            [TestChild::class, function($query) { return $query->where("parentchar", "contains", "K")->get(); },[]],
            [TestChild::class, function($query) { return $query->where('childchar','=',"DEF")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where('childchar','=',"WWW")->get(); },[17]],
            [TestChild::class, function($query) { return $query->where('childchar','=',"QQQ")->get(); },[]],
            [TestChild::class, function($query) { return $query->where('childchar','<',"EEE")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where('childchar','>',"QQQ")->get(); },[17,19,20,22,23]],
            [TestChild::class, function($query) { return $query->where("childchar", "<>", "DEF")->get(); }, [17,19,20,22,23]],
            [TestChild::class, function($query) { return $query->where("childchar", "in", ["DEF","WWW"])->get(); },[17,18,21]],
            [TestChild::class, function($query) { return $query->where("childchar", "begins with", "D")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where("childchar", "begins with", "Q")->get(); },[23]],
            [TestChild::class, function($query) { return $query->where("childchar", "begins with", "K")->get(); },[]],
            [TestChild::class, function($query) { return $query->where("childchar", "ends with", "F")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where("childchar", "ends with", "O")->get(); },[20]],
            [TestChild::class, function($query) { return $query->where("childchar", "ends with", "K")->get(); },[]],
            [TestChild::class, function($query) { return $query->where("childchar", "contains", "O")->get(); },[20]],
            [TestChild::class, function($query) { return $query->where("childchar", "contains", "W")->get(); },[17,22,23]],
            [TestChild::class, function($query) { return $query->where("childchar", "contains", "K")->get(); },[]],
            // enum
            [TestParent::class, function($query) { return $query->where( "parentenum", "=", "testA")->get(); }, [12]],
            [TestParent::class, function($query) { return $query->where( "parentenum", "=", "testB")->get(); }, [10,15,18,22,26]],
            [TestChild::class, function($query) { return $query->where( "parentenum", "=", "testB")->get(); }, [18,22]],
            [TestChild::class, function($query) { return $query->where( "childenum", "=", "testA")->get(); }, []],
            [TestChild::class, function($query) { return $query->where( "childenum", "=", "testB")->get(); }, [18,22]],
            // Calculated fields
            [TestParent::class, function($query) { return $query->where('parentcalc','=',"123A")->get(); },[10,12,17,26]],
            [TestParent::class, function($query) { return $query->where('parentcalc','=',"111A")->get(); },[9]],
            [TestParent::class, function($query) { return $query->where('parentcalc','=',"5A")->get(); },[]],
            [TestParent::class, function($query) { return $query->where('parentcalc','<',"300A")->get(); },[9,10,11,12,13,17,26]],
            [TestParent::class, function($query) { return $query->where('parentcalc','>',"800A")->get(); },[19,25]],
            [TestParent::class, function($query) { return $query->where('parentcalc','<=',"123A")->get(); },[9,10,12,17,26]],
            [TestParent::class, function($query) { return $query->where('parentcalc','>=',"800A")->get(); },[18,19,25]],
            [TestParent::class, function($query) { return $query->where("parentcalc", "<>", "123A")->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class, function($query) { return $query->where("parentcalc", "!=", "123A")->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class, function($query) { return $query->where("parentcalc", "in", ["111A","123A"])->get(); },[9,10,12,17,26]],
            [TestParent::class, function($query) { return $query->where("parentcalc", "begins with", "2")->get(); },[11,13]],
            [TestParent::class, function($query) { return $query->where("parentcalc", "ends with", "3A")->get(); },[10,12,17,24,26]],
            [TestParent::class, function($query) { return $query->where("parentcalc", "contains", "5")->get(); },[14,21,23]],
            [TestChild::class, function($query) { return $query->where('parentcalc','=',"123A")->get(); },[17]],
            [TestChild::class, function($query) { return $query->where('parentcalc','=',"432A")->get(); },[22]],
            [TestChild::class, function($query) { return $query->where('parentcalc','=',"5A")->get(); },[]],
            [TestChild::class, function($query) { return $query->where('parentcalc','<',"300A")->get(); },[17]],
            [TestChild::class, function($query) { return $query->where('parentcalc','>',"800A")->get(); },[19]],
            [TestChild::class, function($query) { return $query->where('parentcalc','<=',"345A")->get(); },[17,23]],
            [TestChild::class, function($query) { return $query->where('parentcalc','>=',"800A")->get(); },[18,19]],
            [TestChild::class, function($query) { return $query->where("parentcalc", "<>", "123A")->get(); }, [18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where("parentcalc", "!=", "123A")->get(); }, [18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where("parentcalc", "in", ["800A","123A"])->get(); },[17,18]],
            [TestChild::class, function($query) { return $query->where("parentcalc", "begins with", "5")->get(); },[21]],
            [TestChild::class, function($query) { return $query->where("parentcalc", "ends with", "3A")->get(); },[17,24]],
            [TestChild::class, function($query) { return $query->where("parentcalc", "contains", "8")->get(); },[18,21]],
            [TestChild::class, function($query) { return $query->where('childcalc','=',"777B")->get(); },[17,24]],
            [TestChild::class, function($query) { return $query->where('childcalc','=',"900B")->get(); },[19]],
            [TestChild::class, function($query) { return $query->where('childcalc','=',"123A")->get(); },[]],
            [TestChild::class, function($query) { return $query->where('childcalc','<',"340B")->get(); },[21,22]],
            [TestChild::class, function($query) { return $query->where('childcalc','>',"800B")->get(); },[18,19]],
            [TestChild::class, function($query) { return $query->where('childcalc','<=',"321B")->get(); },[21,22]],
            [TestChild::class, function($query) { return $query->where('childcalc','>=',"801B")->get(); },[18,19]],
            [TestChild::class, function($query) { return $query->where("childcalc", "<>", "777B")->get(); }, [18,19,20,21,22,23]],
            [TestChild::class, function($query) { return $query->where("childcalc", "!=", "777B")->get(); }, [18,19,20,21,22,23]],
            [TestChild::class, function($query) { return $query->where("childcalc", "in", ["801B","777B"])->get(); },[17,18,24]],
            [TestChild::class, function($query) { return $query->where("childcalc", "begins with", "8")->get(); },[18]],
            [TestChild::class, function($query) { return $query->where("childcalc", "ends with", "1B")->get(); },[18,22]],
            [TestChild::class, function($query) { return $query->where("childcalc", "contains", "2")->get(); },[21,22]],
 */           // tags
            [TestParent::class, function($query) { return $query->where('tags','contains','TagA')->get(); },[12,17,22]],
            [TestParent::class, function($query) { return $query->where('tags','has','TagB.TagC')->get(); },[9,12,19,22]],
            [TestParent::class, function($query) { return $query->where('tags','has','TagZ')->get(); },[]],
            [TestParent::class, function($query) { return $query->where('tags','has not','TagB.TagC')->get(); },[10,11,13,14,15,16,17,18,20,21,23,24,25,26]],
            [TestParent::class, function($query) { return $query->where('tags','one of',['TagE','TagF'])->get(); },[9,10,11,12,19,20,21,22]],
            [TestParent::class, function($query) { return $query->where('tags','none of',['TagF','TagE'])->get(); },[13,14,15,16,17,18,23,24,25,26]],
            [TestParent::class, function($query) { return $query->where('tags','all of',['TagA','TagB'])->get(); },[17]],
            [TestChild::class, function($query) { return $query->where('tags','has','TagD')->get(); },[17,19]],
            [TestChild::class, function($query) { return $query->where('tags','one of',['TagE','TagF'])->get(); },[19,20,21,22]],
            [TestChild::class, function($query) { return $query->where('tags','none of',['TagF','TagE'])->get(); },[17,18,23,24]],
            [TestChild::class, function($query) { return $query->where('tags','all of',['TagA','TagB'])->get(); },[17]],
            
            [TestParent::class, function($query) { return $query->where( "parentsarray", "has", "DEFG")->get(); }, [10,14]],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "has", "Non existing")->get(); }, []],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "has", "Muse")->get(); }, [22]],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "has not", "ABCD")->get(); }, [9,11,12,14,15,16,17,18,19,20,21,22,23,24,25,26]],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "one of", ["ABCD", "DEFG"])->get(); }, [10,13,14]],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "none of", ["ABCD", "DEFG","ZZZZ"])->get(); }, [9,11,12,15,16,17,18,19,20,21,23,24,25,26]],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "all of", ["ABCD", "DEFG"])->get(); }, [10]],
            [TestParent::class, function($query) { return $query->where( "parentsarray", "empty", null)->get(); }, [12,15,16,20,21,23,24,25,26]],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "has", "DEFG")->get(); }, []],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "has", "Muse")->get(); }, [22]],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "has not", "ABCD")->get(); }, [17,18,19,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "one of", ["HELLO","Iron Maiden"])->get(); }, [19,22]],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "none of", ["HALLO","ZZZZ"])->get(); }, [17,18,20,21,23,24]],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "all of", ["HALLO","HOLA"])->get(); }, [19]],
            [TestChild::class, function($query) { return $query->where( "parentsarray", "empty", null)->get(); }, [20,21,23,24]],
            [TestChild::class, function($query) { return $query->where( "childsarray", "has", "ABCD")->get(); }, [20]],
            [TestChild::class, function($query) { return $query->where( "childsarray", "has", "Non existing")->get(); }, []],
            [TestChild::class, function($query) { return $query->where( "childsarray", "has", "Muse")->get(); }, []],
            [TestChild::class, function($query) { return $query->where( "childsarray", "has not", "ABCD")->get(); }, [17,18,19,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "childsarray", "one of", ["ABCD","Yea"])->get(); }, [18,20]],
            [TestChild::class, function($query) { return $query->where( "childsarray", "none of", ["ABCD","Yea","OPQRSTU"])->get(); }, [19,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "childsarray", "all of", ["Yea","Yupp"])->get(); }, [18]],
            [TestChild::class, function($query) { return $query->where( "childsarray", "empty", null)->get(); }, [19,21,22,23]],
            // Object fields
            [TestParent::class, function($query) { return $query->where('parentobject','=',1)->get(); },[9]],
            [TestParent::class, function($query) { return $query->where("parentobject","=",3)->get(); },[17]],
            [TestParent::class, function($query) { return $query->where("parentobject",'=',4)->get(); },[10,12,18]],
            [TestParent::class, function($query) { return $query->where("parentobject",'=',null)->get(); },[13,14,15,16,21,22,23,24,26]],
            [TestParent::class, function($query) { return $query->where("parentobject","!=",null)->get(); },[9,10,11,12,17,18,19,20,25]],
            [TestParent::class, function($query) { return $query->where("parentobject","in",[1,3,4])->get(); },[9,10,12,17,18]],
            [TestChild::class, function($query) { return $query->where("parentobject","=",3)->get(); },[17]],
            [TestChild::class, function($query) { return $query->where("parentobject",'=',4)->get(); },[18]],
            [TestChild::class, function($query) { return $query->where("parentobject",'=',null)->get(); },[21,22,23,24]],
            [TestChild::class, function($query) { return $query->where("parentobject","!=",null)->get(); },[17,18,19,20]],
            [TestChild::class, function($query) { return $query->where("parentobject","in",[1,3,4])->get(); },[17,18]],
            [TestChild::class, function($query) { return $query->where("childobject","=",3)->get(); },[17,19]],
            [TestChild::class, function($query) { return $query->where("childobject",'=',4)->get(); },[18]],
            [TestChild::class, function($query) { return $query->where("childobject",'=',null)->get(); },[20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where("childobject","!=",null)->get(); },[17,18,19]],
            [TestChild::class, function($query) { return $query->where("childobject","in",[3,4])->get(); },[17,18,19]],
            // array of objects
            [TestParent::class, function($query) { return $query->where( "parentoarray","has",3)->get(); },[9,10,13,18]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","has",8)->get(); },[]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","has",7)->get(); },[11,19]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","one of",[1,2])->get(); },[9,10,11,13,14,18,19]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","all of",[1,2])->get(); },[10,13,18]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","all of",[1,7])->get(); },[]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","none of",[1,2,3])->get(); },[12,15,16,17,20,21,22,23,24,25,26]],
            [TestParent::class, function($query) { return $query->where( "parentoarray","empty",null)->get(); }, [12,15,16,20,21,23,24,25,26]],
            
            [TestChild::class, function($query) { return $query->where( "parentoarray","has",3)->get(); },[18]],
            [TestChild::class, function($query) { return $query->where( "parentoarray","has",8)->get(); },[]],
            [TestChild::class, function($query) { return $query->where( "parentoarray","one of",[1,2])->get(); },[18,19]],
            [TestChild::class, function($query) { return $query->where( "parentoarray","all of",[1,2])->get(); },[18]],
            [TestChild::class, function($query) { return $query->where( "parentoarray","all of",[1,7])->get(); },[]],
            [TestChild::class, function($query) { return $query->where( "parentoarray","none of",[1,2,3])->get(); },[17,20,21,22,23,24]],
            [TestChild::class, function($query) { return $query->where( "parentoarray","empty",null)->get(); }, [20,21,23,24]],
            
            [TestChild::class, function($query) { return $query->where( "childoarray","has",1)->get(); },[20,24]],
            [TestChild::class, function($query) { return $query->where( "childoarray","has",8)->get(); },[]],
            [TestChild::class, function($query) { return $query->where( "childoarray","one of",[3,4])->get(); },[17,20]],
            [TestChild::class, function($query) { return $query->where( "childoarray","all of",[4,5])->get(); },[17]],
            [TestChild::class, function($query) { return $query->where( "childoarray","all of",[1,7])->get(); },[]],
            //@todo no expected result        [TestChild::class, function($query) { return $query->where( "childoarray","none of")->get(); },[1,2,3])->get(); },[17,18,19,21,22,23,25]],
            [TestChild::class, function($query) { return $query->where( "childoarray","empty",null)->get(); },[19,21,22,23]],
            
            ];
    }
}