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

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Objects\ORMObject;

/**
 * The implementation for storing an object into a mysql/maria database
 * 
 * @author klaus
 */
class FileStorage extends StorageBase
{
    
    protected function doLoad(int $id)
    {
        $this->loadClassTables($id);
        $this->loadArrays($id);
        $this->loadTags($id);
        $this->loadAttributes($id);
        $this->loadCalculated($id);
    }
    
    private function loadClassTables(int $id)
    {
        
    }
    
    private function loadArray(int $id)
    {
        
    }
    
    private function loadTags(int $id)
    {
        
    }
    
    private function loadAttributes(int $id)
    {
        
    }
    
    private function loadCalculated(int $id)
    {
        
    }
    
    protected function doStore(): int
    {
        
    }
    
    protected function doUpdate(int $id)
    {
        
    }
    
    protected function doDelete(int $id)
    {
        
    }
    
    protected function doMigrate()
    {
        
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
