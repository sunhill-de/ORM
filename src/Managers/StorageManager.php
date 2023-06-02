<?php
 
/**
 * @file StorageManager.php
 * Provides the StorageManager object. This object provides an interface to the
 * storage mechanism. The ORMObject should call Storage::createStorage() to get 
 * the according storage. The manager itself decides what storage should be created
 * (depending on configuration)
 * 
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-04-27
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerTagTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Storage\Mysql\MysqlStorageSupport;

/**
 * The StorageManager is accessed via the Storage facade. It's a singelton class
 */
class StorageManager 
{

    /**
     * Creates a new storage object and returns it
     * 
     * @return StorageBase
     */
    public function createStorage(Property $object): StorageBase
    {
        switch (env('ORM_STORAGE_TYPE', 'mysql')) {
            case 'mysql':
                return new MysqlStorage($object);                
        }
    }
    
    public function tagQuery(): BasicQuery
    {
        switch (env('ORM_STORAGE_TYPE', 'mysql')) {
            case 'mysql':
                $storage_support = new MysqlStorageSupport();
                break;
        }
        return $storage_support->tagQuery();
    }
    
    public function attributeQuery(): BasicQuery
    {
        switch (env('ORM_STORAGE_TYPE', 'mysql')) {
            case 'mysql':
                $storage_support = new MysqlStorageSupport();
                break;
        }
        return $storage_support->attributeQuery();
    }
}
 
