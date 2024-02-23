<?php
/**
 * @file AbstractStorage.php
 * The basic class for storages. While properties are responsible for the processing of data
 * a storage is responsible for accessing and storing data.
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

abstract class AbstractStorage
{
    
    /**
     * Returns the required read capability or null if there is none
     * 
     * @param string $name
     * @return string
     */
    abstract public function getReadCapability(string $name): ?string;
    
    /**
     * Returns if the property is readable
     * 
     * @param string $name
     * @return bool
     */
    abstract public function getIsReadable(string $name): bool;
    
    /**
     * Performs the retrievement of the value
     * 
     * @param string $name
     */
    abstract protected function doGetValue(string $name);
    
    /**
     * Prepares the retrievement of the value
     * 
     * @param string $name
     */
    protected function prepareGetValue(string $name)
    {
        
    }
    
    /**
     * Gets the given value
     * 
     * @param string $name
     * @return unknown
     */
    public function getValue(string $name)
    {
        $this->prepareGetValue($name);
        return $this->doGetValue($name);
    }
    
    /**
     * Returns the required write capability or null if there is none
     * 
     * @param string $name
     * @return string|NULL
     */
    abstract public function getWriteCapability(string $name): ?string;
    
    /**
     * Returns if this property is writeable
     * @param string $name
     * @return bool
     */
    abstract public function getIsWriteable(string $name): bool;
    
    /**
     * Returns the modify capability or null if there is none
     * 
     * @param string $name
     * @return string|NULL
     */
    abstract public function getModifyCapability(string $name): ?string;
        
    /**
     * Performs the setting of the value
     * 
     * @param string $name
     * @param unknown $value
     */
    abstract protected function doSetValue(string $name, $value);
    
    /**
     * Perfoms action after setting the value
     * 
     * @param string $name
     * @param unknown $value
     */
    protected function postprocessSetValue(string $name, $value)
    {
        
    }
    
    /**
     * Sets the given value
     * 
     * @param string $name
     * @param unknown $value
     */
    public function setValue(string $name, $value)
    {
        $this->doSetValue($name, $value);
        $this->postprocessSetValue($name, $value);
    }
    
    /**
     * Returns if this storage was modified
     *
     * @return bool
     */
    abstract public function isDirty(): bool;

    /**
     * For cached storages performs the flush of the cache. Has to be called by property.
     */
    public function commit()
    {
        
    }
    
    /**
     * For cached storages performs the reollback of the cache. Has to be called
     * by property.
     * 
     */
    public function rollback()
    {
        
    }

    /**
     * Returns if the value was already initialized with a value
     * 
     * @return bool
     */
    abstract public function getIsInitialized(string $name): bool;
    
}