<?php
/**
 * @file StorageBase.php
 * The basic class for storages (at the moment there is only StorageMySQL)
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Property;

/**
 * Basisklasse für Storages. Die abgeleiteten Klassen müssen die protected property $modules definieren, welche die eigentlichen
 * Module für die Entity-Klassen läd.
 * Die für das Interface zu den Objekten wichtigen Methoden sind:
 * - loadObject($id)
 * - insertObject()
 * - updateObject($id)
 * - deleteObject($id)
 * 
 * @author lokal
 *
 */
abstract class StorageBase  
{
    
    /** 
     * Stores the calling ORMObject
     * @var ORMObject
     */
    protected $caller;
    
    protected $entities = [];
    
    /**
     * The constructor takes the calling object as a parameter
     * @param unknown $caller
     */
    public function __construct(Property $caller) 
    {
        $this->caller = $caller;    
    }
    
    /**
     * Returns the calling object
     * @return ORMObject
     */
    public function getCaller()
    {
        return $this->caller;
    }
    
    
    /**
     * @retval array Wrapper for getInheritance() of the caller
     */
    public function getInheritance() 
    {
        return $this->caller->getInheritance(true);
    }
    
    
    /**
     * Returns the entry with name $name or null if not defined
     */
    public function getEntity(string $name) 
    {
        if (!isset($this->entities[$name])) {
            return null;
        } else {
            return $this->entities[$name];
        }
    }
    
    /**
     * Wrapper for getEntity()
     * @param string $name
     * @return unknown
     */
    public function __get(string $name) 
    {
        return $this->getEntity($name);
    }
    
    /**
     * Writes the entry with the name $name and the value $value
     * @param string $name
     * @param unknown $value
     */
    public function setEntity(string $name, $value) 
    {
        $this->entities[$name] = $value;
        return $this;
    }
    
    public function hasEntity(string $name): bool
    {
        return isset($this->entities[$name]);    
    }
    
    /**
     * Wrapper for setEntity()
     */
    public function __set(string $name, $value) 
    {
        return $this->setEntity($name,$value);
    }    
    
    abstract protected function doLoad(int $id);
    abstract protected function doStore(): int;
    abstract protected function doUpdate(int $id);
    abstract protected function doDelete(int $id);
    abstract protected function doMigrate();
    abstract protected function doPromote();
    abstract protected function doDegrade();
    abstract protected function doSearch();
    abstract protected function doDrop();
    
    public function load(int $id)
    {
        return $this->doLoad($id);        
    }

    public function loadObject(int $id)
    {
        return $this->load($id);    
    }
    
    public function Store(): int
    {
        return $this->doStore();
    }
    
    public function insertObject(): int
    {
        return $this->Store();    
    }
    
    public function Update(int $id)    
    {
        return $this->doUpdate($id);
    }
    
    public function Delete(int $id)
    {
        return $this->doDelete($id);    
    }
    
    public function Migrate()
    {
        return $this->doMigrate();    
    }
    
    public function Promote()
    {
        return $this->doPromote();    
    }
    
    public function Degrade()
    {
        return $this->doDegrade();
    }
    
    public function Drop()
    {
        return $this->doDrop();    
    }
    
    public function Search()
    {
        return $this->doSearch();    
    }
    
}
