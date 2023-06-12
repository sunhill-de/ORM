<?php
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Storage\StorageBase;

trait HandlesStorage
{
    /**
     * Loads the collection with the id $id from the storage
     * In this case it sets the state to preloaded. Accessing the properties wouhl than execute
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
     * Fills the storage with dummy values, so the storage knows what property to expcect
     * 
     * @param StorageBase $storage
     * @param string $class
     */
    protected function prepareLoadForClass(StorageBase $storage, string $class)
    {
        $properties = $class::getPropertyDefinition();
        foreach ($properties as $property) {
            $storage->setEntity($property->getName(),null, $class::getInfo('table'), $property::class);
        }
    }
    
    protected function prepareLoad(StorageBase $storage)
    {
        $hirarchy = $this->getClassList();
        foreach ($hirarchy as $class) {
            $this->prepareLoadForClass($storage, $class);
        }
    }
    
    /**
     * Does finally load the collection from the database
     */
    protected function finallyLoad()
    {
        $storage = Storage::createStorage($this);
        $this->prepareLoad($storage);
        $storage->load($this->getID());
        $this->setState('loading');
        $this->loadFromStorage($storage);
    }
 
    protected function doCommit()
    {
        $storage = Storage::createStorage($this);
        if (empty($this->getID())) {
            $this->preCreation($storage);
            $this->createObject($storage);
            $this->postCreation($storage);
        } else {
            $this->preUpdate($storage);
            $this->updateObject($storage);
            $this->postUpdate($storage);
        }
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
    
}

