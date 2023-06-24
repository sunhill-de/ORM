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

use Sunhill\ORM\Properties\Property;
use Illuminate\Testing\Assert as PHPUnit;

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
    
    public function createEntity(string $name, string $storage_id): StorageElement
    {
        $element = new StorageElement();
        $element->setName($name)->setStorageID($storage_id);
        $this->entities[$name] = $element;
        if (isset($this->storage_ids[$storage_id])) {
            $this->storage_ids[$storage_id] = [$name=>$element];
        } else {
            $this->storage_ids[$storage_id][$name] = $element;
        }
        return $element;
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
        return $this->getEntity($name)->getValue();
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
        $result = [];
        foreach ($this->entities as $key => $value) {
            if ($value->getStorageID() == $storage_id) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    public function getEntitiesOfType(string $type)
    {
        $result = [];
        foreach ($this->entities as $key => $value) {
            if ($value->getType() == $type) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    public function getAllEntities()
    {
        return $this->entities;    
    }
    
    /**
     * Wrapper for setEntity()
     */
    public function __set(string $name, $value) 
    {
        return $this->getEntity($name)->setValue($value);
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
 
    public function assertStorageEquals(StorageBase $test, bool $both = false): bool
    {
        foreach ($this->entities as $key => $value) {
            PHPUnit::assertTrue($test->hasEntity($key),"The tested storage doesn't contain '$key'");
            PHPUnit::assertEquals($value->getType(),$test->getEntity($key)->getType(),"In key '$key' the expected type '".$value->getType()."' doesn't equal '".$test->getEntity($key)->getType()."'");
            PHPUnit::assertEquals($value->getStorageID(),$test->getEntity($key)->getStorageID(),"In key '$key' the expected storage ID '".$value->getStorageID()."' doesn't equal '".$test->getEntity($key)->getStorageID()."'");
            PHPUnit::assertEquals($value->getValue(),$test->getEntity($key)->getValue(),"In key '$key' the expected value '".$value->getValue()."' doesn't equal '".$test->getEntity($key)->getValue()."'");
            PHPUnit::assertEquals($value->getShadow(),$test->getEntity($key)->getShadow(),"In key '$key' the expected shadow '".$value->getShadow()."' doesn't equal '".$test->getEntity($key)->getShadow()."'");
        }
        if ($both) {
            return $test->assertStorageEquals($this, false);
        }
        return true;
    }
    
}
