<?php
/**
 * @file AbstractIDStorage.php
 * The basic class for storages that are cached AND use an id. 
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

use Sunhill\ORM\Storage\Exceptions\IDNotSetException;

abstract class AbstractIDStorage extends AbstractCachedStorage
{
    
    /**
     * The current if in the storage
     * 
     * @var integer
     */
    protected $id = 0;

    /**
     * Setter for ID
     * 
     * @param int $id
     * @return AbstractCachedStorage
     */
    public function setID(int $id): AbstractCachedStorage
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Getter for ID
     * 
     * @return int
     */
    public function getID(): int
    {
        return $this->id;    
    }
    
    /**
     * An abstract ID-Storage is stored if it already has an ID
     * 
     * @return bool
     */
    protected function isAlreadyStored(): bool
    {
        return !empty($this->getID());
    }
    
    /**
     * Reads the storage from the underlying storage with the given ID
     * 
     * @param int $id
     */
    abstract protected function readFromID(int $id);
    
    /**
     * Calls the (here) abstract method readFromID() with the current ID
     */
    protected function doReadFromUnderlying()
    {
        if (empty($this->getID())) {
            throw new IDNotSetException("Read called with no ID");
        }
        $this->readFromID($this->getID());
    }
    
    abstract protected function writeToID(): int;
    
    protected function doWriteToUnderlying()
    {
        $this->setID($this->writeToID());
    }
    
    abstract protected function updateToID(int $id);
    
    protected function doUpdateUnderlying()
    {
        if (empty($this->getID())) {
            throw new IDNotSetException("Update called with no ID");
        }
        $this->updateToID($this->getID());
    }
 
    public function getIsInitialized(string $name): bool
    {
        return isset($this->values[$name]);
    }
    
}