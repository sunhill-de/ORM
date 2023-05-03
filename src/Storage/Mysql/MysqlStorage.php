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
        $storage_helper = new MysqlLoad($this);
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
        
    }
    
    protected function doDegrade()
    {
        
    }
    
    protected function doSearch()
    {
        
    }
 
    protected function doDrop()
    {
        
    }
}
