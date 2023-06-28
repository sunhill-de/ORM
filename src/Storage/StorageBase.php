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
use Sunhill\ORM\Objects\PropertiesCollection;

/**
 * 
 * @author lokal
 *
 */
abstract class StorageBase  
{
    
    public function setCollection(PropertiesCollection $collection)
    {
        $this->collection = $collection;    
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
            if (is_array($value->getValue())) {
                PHPUnit::assertEquals($value->getValue(),$test->getEntity($key)->getValue(),"In key '$key' the expected values doesn't equal.");
            } else {
                PHPUnit::assertEquals($value->getValue(),$test->getEntity($key)->getValue(),"In key '$key' the expected value '".$value->getValue()."' doesn't equal '".$test->getEntity($key)->getValue()."'");                
            }
            PHPUnit::assertEquals($value->getShadow(),$test->getEntity($key)->getShadow(),"In key '$key' the expected shadow '".$value->getShadow()."' doesn't equal '".$test->getEntity($key)->getShadow()."'");
        }
        if ($both) {
            return $test->assertStorageEquals($this, false);
        }
        return true;
    }
    
}
