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
use Sunhill\ORM\Query\BasicQuery;

/**
 * 
 * @author lokal
 *
 */
abstract class StorageBase  
{
    
    protected $source_type = 'collection';
    
    public function setSourceType(string $type)
    {
        $this->source_type = $type;
        return $this;
    }
    
    public function getSourceType(): string
    {
        return $this->source_type;    
    }
    
    protected $entities = [];
    
    protected $storage_ids = [];
    
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
        return $this->getEntity($name)->value;
    }
    
    /**
     * Writes the entry with the name $name and the value $value
     * @param string $name
     * @param unknown $value
     */
    public function setEntity(string $name, $value = null, string $storage_id = '', string $type = '', $shadow = null) 
    {
        if (isset($this->entities[$name])) {
            $this->entities[$name]->value = $value;
            return $this;
        }
        $entry = new \StdClass();
        $entry->name = $name;
        $entry->storage_id = $storage_id;
        $entry->type = $type;
        $entry->value = $value;
        $entry->shadow = $shadow;
        
        $this->entities[$name] = $entry;
        if (isset($this->storage_ids[$storage_id])) {
            $this->storage_ids[$storage_id][$name] = $entry;
        } else {
            $this->storage_ids[$storage_id] = [$name => $entry ];            
        }
        return $this;
    }
    
    public function hasEntity(string $name): bool
    {
        return isset($this->entities[$name]);    
    }
    
    public function getStorageIDs()
    {
        return array_keys($this->storage_ids);    
    }
    
    public function getEntitiesOfStorageID(string $storage_id)
    {
        if (!isset($this->storage_ids[$storage_id])) {
            return null;
        }
        return $this->storage_ids[$storage_id];
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
