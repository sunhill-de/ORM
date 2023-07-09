<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Illuminate\Database\Eloquent\Collection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

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
            [DummyCollection::class, function($query) { return $query->where('dummyint',123)->count(); }, 3],
            [DummyCollection::class, function($query) { return $query->where('dummyint',123)->first(); }, 1],
            [DummyCollection::class, function($query) { return $query->where('dummyint',234)->get(); }, [2]],
            [DummyCollection::class, function($query) { return $query->where('dummyint',666)->get(); }, []],            
            
            [ComplexCollection::class, function($query) { return $query->where('field_int','<',123)->get(); }, [9]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','<=',123)->get(); }, [9,10,12,17,26]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','<>',123)->get(); }, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','>',900)->get(); }, [25]],
            [ComplexCollection::class, function($query) { return $query->where('field_int','>=',900)->get(); }, [19,25]],            

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
            
            ];
    }
}