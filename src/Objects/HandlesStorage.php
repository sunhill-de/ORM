<?php
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Facades\Storage;

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
     * Umplements the lazy loading mechanism, that a collection is only loaded if accessed
     *
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\NonAtomarProperty::checkLoadingState()
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
        $storage = Storage::createStorage($this);
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

