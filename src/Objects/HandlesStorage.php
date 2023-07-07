<?php
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Facades\ObjectData;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;

trait HandlesStorage
{
    /**
     * Loads the collection with the id $id from the storage
     * In this case it sets the state to preloaded. Accessing the properties would then execute
     * the loading mechanism.
     *
     * @param int $id
     */
    public function load(int $id)
    {
        $this->setState('preloaded');
        $this->setID($id);
    }

    /**
     * Forces the collection to load itself from the storage without accessing a property first
     * Mostly for debugging
     */
    public function forceLoading()
    {
        $this->checkLoadingState();    
    }
    
    /**
     * Implements the lazy loading mechanism, that a collection is only loaded if accessed
     *
     */
    protected function checkLoadingState()
    {
        if ($this->getState() == 'preloaded') {
            $this->finallyLoad();
        }
    }

    
    /**
     * Does finally load the collection from the database
     */
    protected function finallyLoad()
    {
        $storage = Storage::createStorage();
        $storage->setCollection($this);
        
        $this->setState('loading');
        $storage->dispatch('load');
        $this->setState('normal');
    }
 
    protected function doCommit()
    {
        $storage = Storage::createStorage();
        $storage->setCollection($this);
        
        if (empty($this->getID())) {
            $this->prepareStore();
            $storage->dispatch('store');
        } else {
            $this->prepareUpdate();
            $storage->dispatch('update');
        }
    }
    
    protected function prepareStore()
    {
    }
    
    protected function prepareUpdate()
    {
    }
    
    protected function createObject($store)
    {
        $this->walkProperties(function ($property) use ($store){
            $property->storeToStorage($store);
        });
       $this->setID($store->store());
       return $this->getID();
    }
    
    protected function updateObject($store)
    {
        $this->walkProperties(function ($property) use ($store){
            $property->updateToStorage($store);
        });
        $store->update($this->getID());
    }
    
    protected function doRollback()
    {
        
    }

    /**
     * Fills the storage with dummy values, so the storage knows what property to expcect
     *
     * @param StorageBase $storage
     * @param string $class
     */
    protected static function prepareStorageForClass($storage, string $class)
    {
        $properties = $class::getPropertyDefinition();
        foreach ($properties as $property) {
            $entity = $storage->createEntity($property->getName(), $class::getInfo('table'))->setType($property::class);
            if (in_array($property::class,[PropertyArray::class,PropertyMap::class])) {
                $entity->setElementType($property->getElementType());
            }
        }
    }
    
    protected static function prepareStorage($storage)
    {
        $hirarchy = static::getClassList();
        foreach ($hirarchy as $class) {
            static::prepareStorageForClass($storage, $class);
        }
    }
    
}

