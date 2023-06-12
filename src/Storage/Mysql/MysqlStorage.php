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

/**
 * The implementation for storing a property into a mysql/maria database
 * 
 * @author klaus
 */
class MysqlStorage extends StorageBase 
{
    
    protected function callAppropriateModule(string $method, $payload = null)
    {
        $call_gate = 'do'.$method;
        if ('object' == $this->source_type) {
            $module = '\\Sunhill\\ORM\\Storage\\Mysql\\Objects\\MysqlObject'.$method;
        } else if ('collection' == $this->source_type) {
            $module = '\\Sunhill\\ORM\\Storage\\Mysql\\Collections\\MysqlCollection'.$method;
        } else {
            throw new \Exception('Unhandled storage type: '.$this->source_type);
        }
        $storage_helper = new $module($this);
        return $storage_helper->$call_gate($payload);
    }
    
    /**
     * Loads the property with the given id from the storage
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\StorageBase::doLoad()
     */
    protected function doLoad(int $id)
    {
        $this->callAppropriateModule('Load', $id);
    }
    
    protected function doStore(): int
    {
        return $this->callAppropriateModule('Store');
    }
    
    protected function doUpdate(int $id)
    {
        $this->callAppropriateModule('Update', $id);
    }
    
    protected function doDelete(int $id)
    {
        $this->callAppropriateModule('Delete', $id);
    }
    
    protected function doMigrate()
    {
        $this->callAppropriateModule('Migrate');
    }
    
    protected function doPromote()
    {
        // Collections can't be promoted
        $storage_helper = new MysqlObjectPromote($this);
        return $storage_helper->doPromote();        
    }
    
    protected function doDegrade()
    {
        // Collections can't be degraded
        $storage_helper = new MysqlObjectDegrade($this);
        return $storage_helper->doDegrade();        
    }
    
    protected function doSearch()
    {
        $this->callAppropriateModule('Search');
    }
 
    protected function doDrop()
    {
        $this->callAppropriateModule('Drop');
    }
       
}
