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
 * The implementation for storing an object into a mysql/maria database
 * 
 * @author klaus
 */
class MysqlStorage extends StorageBase 
{
      
    // **************************** Load ********************************************
    protected function doLoad(int $id)
    {
        $mysql_load = new MysqlLoad($this);
        $mysql_load->doLoad($id);
    }
    
    protected function doStore(): int
    {
        $mysql_store = new MysqlStore($this);
        return $mysql_store->doStore();        
    }
    
    protected function doUpdate(int $id)
    {
        $mysql_update = new MysqlUpdate($this);
        return $mysql_update->doUpdate($id);        
    }
    
    protected function doDelete(int $id)
    {
        
    }
    
    protected function doMigrate()
    {
        $mysql_migrate = new MysqlMigrate($this);
        return $mysql_migrate->doMigrate();        
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
     
}
