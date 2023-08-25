<?php
/**
 * @file HandlesStorage.php
 * Utilities for the interaction with storages
 * Lang en (complete)
 * Reviewstatus: 2023-08-25
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 */

namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Facades\Storage;

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
        
}

