<?php
/**
 * @file MysqlStorage.php
 * The storage that stores objects in an mysql/maria-database
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2023-04-27
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\PropertyCollection;

/**
 * The implementation for storing a property into a mysql/maria database
 * 
 * @author klaus
 */
class MysqlStorage extends StorageBase 
{
      
    /**
     * Loads the property with the given id from the storage
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\StorageBase::doLoad()
     */
    protected function doLoad(int $id)
    {
        if (is_a($this->getCaller(),ORMObject::class)) {
            $storage_helper = new MysqlLoadObject($this);
        } else if (is_a($this->getCaller(),Collection::class)) {
            $storage_helper = new MysqlLoadCollection($this);
        } else {
            throw new \Exception('Unhandled storage class: '.$this->getCaller()->getStorageClass());
        }
        $storage_helper->doLoad($id);
    }
    
    protected function doStore(): int
    {
        if (is_a($this->getCaller(),ORMObject::class)) {
            $storage_helper = new MysqlStoreObject($this);            
        } else if (is_a($this->getCaller(),Collection::class)) {
            $storage_helper = new MysqlStoreCollection($this);            
        } else {
            throw new \Exception('Unhandled storage class: '.$this->getCaller()->getStorageClass());            
        }
        return $storage_helper->doStore();        
    }
    
    protected function doUpdate(int $id)
    {
        if (is_a($this->getCaller(),ORMObject::class)) {
            $storage_helper = new MysqlUpdateObject($this);
        } else if (is_a($this->getCaller(),Collection::class)) {
            $storage_helper = new MysqlUpdateCollection($this);
        } else {
            throw new \Exception('Unhandled storage class: '.$this->getCaller()->getStorageClass());
        }
        return $storage_helper->doUpdate($id);        
    }
    
    protected function doDelete(int $id)
    {
        if (is_a($this->getCaller(),ORMObject::class)) {
            $storage_helper = new MysqlDeleteObject($this);
        } else if (is_a($this->getCaller(),Collection::class)) {
            $storage_helper = new MysqlDeleteCollection($this);
        } else {
            throw new \Exception('Unhandled storage class: '.$this->getCaller()->getStorageClass());
        }
        return $storage_helper->doDelete($id);        
    }
    
    protected function doMigrate()
    {
        $storage_helper = new MysqlMigrate($this);
        return $storage_helper->doMigrate();        
    }
    
    protected function doPromote()
    {
        $storage_helper = new MysqlPromote($this);
        return $storage_helper->doPromote();        
    }
    
    protected function doDegrade()
    {
        $storage_helper = new MysqlDegrade($this);
        return $storage_helper->doDegrade();        
    }
    
    protected function doSearch()
    {
        $storage_helper = new MysqlSearch($this);
        return $storage_helper->doSearch();        
    }
 
    protected function doDrop()
    {
        $storage_helper = new MysqlDrop($this);
        return $storage_helper->doDrop();        
    }
}
