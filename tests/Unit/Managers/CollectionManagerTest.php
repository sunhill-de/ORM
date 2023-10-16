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
        $this->expectException(IsNotACollectionException::class);
        Collections::loadCollection('nonexisting', 2);
    }
    
    public function testIsNotACollection()
    {
        $this->expectException(IsNotACollectionException::class);
        Collections::loadCollection(ORMObject::class, 2);
    }
    
    public function testRegisterCollection()
    {
        Collections::registerCollection(DummyCollection::class);
        $this->assertEquals(DummyCollection::class, Collections::searchCollection('dummycollection'));
        $this->assertEquals(['dummycollection'=>DummyCollection::class], Collections::getRegisteredCollections());
    }
    
}
