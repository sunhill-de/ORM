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

abstract class AbstractCachedStorage
{
    
    /**
     * The current if in the storage
     * @var integer
     */
    protected $id = 0;
    
    protected $values = [];
    
    protected $shadows = [];

    public function setID(int $id): AbstractCachedStorage
    {
        $this->id = $id;
        return $this;
    }
    
    public function getID(): int
    {
        return $this->id;    
    }
    
    abstract protected function doReadFromUnderlying(int $id);
    
    abstract protected function doWriteToUnderlying(): int;
    
    abstract protected function doUpdateUnderlying(int $id);
    
    /**
     * Performs the retrievement of the value
     * 
     * @param string $name
     */
    protected function doGetValue(string $name)
    {
        if (!isset($this->values[$name])) {
            
        }
    }
    
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
    abstract public function getWriteable(string $name): bool;
    
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
    
}