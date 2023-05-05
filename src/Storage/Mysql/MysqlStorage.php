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
        switch ($this->getCaller()->getStorageClass()) {
            case 'Object':
                $storage_helper = new MysqlLoadObject($this);
                break;
            case 'Collection':
                $storage_helper = new MysqlLoadCollection($this);
                break;
            case 'Table':
                $storage_helper = new MysqlLoadTable($this);
                break;
            default:
                throw new \Exception('Unhandled storage class: '.$this->getCaller()->getStorageClass());    
        }
        $storage_helper->doLoad($id);
    }
    
    protected function doStore(): int
    {
        $storage_helper = new MysqlStore($this);
        return $storage_helper->doStore();        
    }
    
    protected function doUpdate(int $id)
    {
        $storage_helper = new MysqlUpdate($this);
        return $storage_helper->doUpdate($id);        
    }
    
    protected function doDelete(int $id)
    {
        $storage_helper = new MysqlDelete($this);
        $storage_helper->doDelete($id);        
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
