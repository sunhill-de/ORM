<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Managers\ClassManager;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;
use Sunhill\ORM\Managers\Exceptions\ClassNotORMException;
use Sunhill\ORM\Managers\Exceptions\ClassNotAccessibleException;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Managers\Exceptions\ClassNameForbiddenException;
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Facades\Collections;
use Sunhill\ORM\Managers\Exceptions\CollectionClassDoesntExistException;
use Sunhill\ORM\Managers\Exceptions\IsNotACollectionException;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyBoolean;

class CollectionManagerTest extends TestCase
{
 
    public function testLoadCollection()
    {
        $collectionMock = \Mockery::mock(Collection::class);
        $collectionMock->shouldReceive('load')->with(2)->andReturn(true);
        $collectionMock->shouldReceive('forceLoading')->andReturn(true);
        
        $collection = Collections::loadCollection(Collection::class, 2);  
        $this->assertEquals(2, $collection->getID());
    }
   
    public function testClassNotExist()
    {
        $this->expectException(ClassNotAccessibleException::class);
        Collections::loadCollection('nonexisting', 2);
    }
    
    public function testIsNotACollection()
    {
        $this->expectException(ClassNotORMException::class);
        Collections::loadCollection(ORMObject::class, 2);
    }
    
    public function testRegisterCollection()
    {
        Collections::registerCollection(DummyCollection::class);
        $info = Collections::searchCollection('dummycollection');
        $this->assertEquals(DummyCollection::class, $info->class);
    }
    
    /**
     * @dataProvider CollectionQueryProvider
     */
    public function testCollectionQuery($callback, $modifier, $expect)
    {
        Collections::registerCollection(DummyCollection::class);
        Collections::registerCollection(ComplexCollection::class);
        
        $query = Collections::query();
        $result = $callback($query);
        
        if (is_callable($modifier)) {
            $result = $modifier($result);
        }
        $this->assertEquals($expect, $result);
        
    }
    
    public static function CollectionQueryProvider()
    {
        return [
            [function($query) { return $query->count(); },null, 2],            
            [function($query) { return $query->first(); },function($result) { return $result->name; }, 'dummycollection'],
            [function($query) { return $query->orderBy('name')->first(); },function($result) { return $result->name; }, 'complexcollection'],

            [function($query) { return $query->where('name','dummycollection')->first(); }, function($value) { return $value->name; }, 'dummycollection'],
            [function($query) { return $query->where('name','like','complex%')->count(); }, null, 1],
            [function($query) { return $query->where('name','like','%collection')->count(); }, null, 2],
            [function($query) { return $query->where('name','like','dummy%')->count(); }, null, 1],
            [function($query) { return $query->whereHasPropertyOfType(PropertyArray::class)->count(); }, null, 1],
            [function($query) { return $query->whereHasPropertyOfType(PropertyDate::class)->count(); }, null, 1],
            [function($query) { return $query->whereHasPropertyOfType(PropertyBoolean::class)->count(); }, null, 1],
            [function($query) { return $query->whereHasPropertyOfName('dummyint')->count(); }, null, 1],
                        
            [function($query) { return $query->where('table','dummycollections')->first(); }, function($value) { return $value->name; }, 'dummycollection'],
            
            ];
    }
}
