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
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionLoad;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionStore;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionUpdate;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectLoad;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectStore;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectUpdate;
use Sunhill\ORM\Storage\Exceptions\ActionNotFoundException;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionDelete;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectDelete;

/**
 * The implementation for storing a property into a mysql/maria database
 * 
 * @author klaus
 */
class MysqlStorage extends StorageBase 
{
    
    public function dispatch(string $action)
    {
        if (is_a($this->getCollection(), ORMObject::class)) {
            return $this->dispatchObject($action);
        }
        if (is_a($this->getCollection(), Collection::class)) {
            return $this->dispatchCollection($action);
        }
        return $this->dispatchOther($action);
    }
    
    protected function dispatchCollection(string $action)
    {
        switch ($action) {
            case 'load':
                return $this->dispatchToAction(MysqlCollectionLoad::class);
                break;
            case 'store':
                return $this->dispatchToAction(MysqlCollectionStore::class);
                break;
            case 'update':
                return $this->dispatchToAction(MysqlCollectionUpdate::class);
                break;
            case 'delete':    
                return $this->dispatchToAction(MysqlCollectionDelete::class);
                break;
        }
    }
    
    protected function dispatchObject(string $action)
    {
        switch ($action) {
            case 'load':
                return $this->dispatchToAction(MysqlObjectLoad::class);
                break;
            case 'store':
                return $this->dispatchToAction(MysqlObjectStore::class);
                break;
            case 'update':
                return $this->dispatchToAction(MysqlObjectUpdate::class);
                break;
            case 'delete':    
                return $this->dispatchToAction(MysqlObjectDelete::class);
                break;
        }        
    }

    protected function dispatchOther(string $action)
    {
        switch ($action) {
            case 'tags':
                return $this->dispatchToAction(MysqlTagQuery::class);
                break;
            case 'attributes':
                return $this->dispatchToAction(MysqlAttributeAction::class);
                break;
            default:
                throw new ActionNotFoundException("The action '$action' is unhandled.");
        }
    }
    
}
