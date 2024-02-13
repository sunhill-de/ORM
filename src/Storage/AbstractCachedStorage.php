<?php
/**
 * @file AbstractCachedStorage.php
 * The basic class for storages that are cached. When writing to such a storage the values 
 * are written delayed to the underlying storage system
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-02-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Storage\Exceptions\PropertyNotFoundException;

abstract class AbstractCachedStorage extends AbstractStorage
{
    
    /**
     * Stores the current values
     * 
     * @var array
     */
    protected $values = [];
    
    /**
     * Stores the backup values (for rollback and dirty)
     * 
     * @var array
     */
    protected $shadows = [];

    /**
     * Read storage from underlying storage
     */
    abstract protected function doReadFromUnderlying();
    
    /**
     * When this storage was not stored before call write function
     */
    abstract protected function doWriteToUnderlying();
    
    /**
     * When this storage was stored before call update function
     */
    abstract protected function doUpdateUnderlying();
    
    /**
     * Returns true, if this storage is already stored 
     * 
     * @return bool
     */    
    abstract protected function isAlreadyStored(): bool;
    
    /**
     * Returns true, when this storage was already loaded
     * 
     * @return bool
     */
    protected function storageIsLoaded(): bool
    {
        return !empty($this->values);    
    }

    /**
     * Checks if the given property. Must only be called after the storage was loaded
     * 
     * @param string $name
     */
    private function checkPropertyExists(string $name)
    {
        // Is the property defined?
        if (!isset($this->values[$name])) {
            // No, throw exception
            throw new PropertyNotFoundException("The property '$name' was not found in this storage.");
        }        
    }
    
    /**
     * Loads the storage if necessary
     */
    private function loadOnDemand()
    {
        // Storage already loaded?
        if (!$this->storageIsLoaded()) {
            $this->doReadFromUnderlying(); // No, load it
        }        
    }
    
    /**
     * Performs the retrievement of the value
     * 
     * @param string $name
     * @throws PropertyNotFoundException When the property is not defined in this storage
     */
    protected function doGetValue(string $name)
    {        
        $this->checkPropertyExists($name);
        
        return $this->values[$name];
    }
    
    /**
     * Prepares the retrievement of the value
     * 
     * @param string $name
     */
    protected function prepareGetValue(string $name)
    {
        $this->loadOnDemand();
    }

    /**
     * Returns true, when the given property was already assigned a value
     * 
     * @param string $name
     * @return bool
     */
    protected function isInitialized(string $name): bool
    {
        return isset($this->values[$name]);
    }
    
    /**
     * Returns true, when the given property was alread modified
     * 
     * @param string $name
     * @return bool
     */
    protected function isModified(string $name): bool
    {
        return isset($this->shadows[$name]);    
    }
    
    /**
     * Handles the value change of a already initialized property
     * 
     * @param string $name
     * @param unknown $value
     */
    private function handleInitializedValue(string $name, $value)
    {
        if ($this->values[$name] <> $value) { // Is there any change?
            if (!$this->isModified($name)) { // Not already shadowed
                $this->shadows[$name] = $this->values[$name]; // No, than store old value to shadow
            }
            $this->values[$name] = $value; // Store value
        }        
    }
    
    /**
     * Handles th value change of a uninitialized property
     * 
     * @param string $name
     * @param unknown $value
     */
    private function handleUninitializedValue(string $name, $value)
    {
        $this->values[$name] = $value;        
    }
    
    /**
     * Performs the setting of the value
     * 
     * @param string $name
     * @param unknown $value
     */
    protected function doSetValue(string $name, $value)
    {
        if ($this->isAlreadyStored()) {
            $this->loadOnDemand();
            $this->checkPropertyExists($name);
        }
        
        if ($this->isInitialized($name)) {
            $this->handleInitializedValue($name, $value);
        } else {
            $this->handleUninitializedValue($name, $value);            
        }
    }
    
    /**
     * Returns if this storage was modified
     * 
     * @return bool
     */
    public function isDirty(): bool
    {
        return !empty($this->shadows) || (!$this->isAlreadyStored() && !empty($this->values));    
    }
    
    /**
     * Depeding on if the storage was already stored call update or write and clean shadow
     */
    protected function doCommit()
    {
        if ($this->isAlreadyStored()) {
            $this->doUpdateUnderlying();
        } else {
            $this->doWriteToUnderlying();
        }
        $this->shadows = [];
    }
    
    /**
     * Only if dirty call doCommit()
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\AbstractStorage::commit()
     */
    public function commit()
    {
        if ($this->isDirty() || !$this->isAlreadyStored()) {
            $this->doCommit();
        }
    }
    
    /**
     * For cached storages performs the reollback of the cache. Has to be called
     * by property.
     * 
     */
    public function rollback()
    {
        foreach ($this->shadows as $name => $value)
        {
            $this->values[$name] = $value;
        }
        $this->shadows = [];
    }
    
}