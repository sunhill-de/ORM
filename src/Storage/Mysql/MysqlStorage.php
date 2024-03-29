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
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionDrop;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectDrop;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionSearch;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectSearch;
use Sunhill\ORM\Storage\Mysql\MysqlTagSearch;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionMigrate;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectDegrade;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectMigrate;
use Sunhill\ORM\Storage\Mysql\Objects\MysqlObjectPromote;
use Illuminate\Support\Facades\DB;

/**
 * The implementation for storing a property into a mysql/maria database
 * 
 * @author klaus
 */
class MysqlStorage extends StorageBase 
{
    
    public function dispatch(string $action, $additional = null, $additional2 = null)
    {
        if (is_a($this->getCollection(), ORMObject::class)) {
            return $this->dispatchObject($action, $additional, $additional2);
        }
        if (is_a($this->getCollection(), Collection::class)) {
            return $this->dispatchCollection($action, $additional, $additional2);
        }
        return $this->dispatchOther($action, $additional, $additional2);
    }
    
    protected function dispatchCollection(string $action, $additional = null, $additional2 = null)
    {
        switch ($action) {
            case 'load':
                return $this->dispatchToAction(MysqlCollectionLoad::class, $additional);
                break;
            case 'store':
                return $this->dispatchToAction(MysqlCollectionStore::class, $additional);
                break;
            case 'update':
                return $this->dispatchToAction(MysqlCollectionUpdate::class, $additional);
                break;
            case 'delete':    
                return $this->dispatchToAction(MysqlCollectionDelete::class, $additional);
                break;
            case 'drop':
                return $this->dispatchToAction(MysqlCollectionDrop::class);
                break;
            case 'search':
                return $this->dispatchToAction(MysqlCollectionSearch::class);
                break;
            case 'migrate':
                return $this->dispatchToAction(MysqlCollectionMigrate::class);
                break;
            default:
                throw new ActionNotFoundException("The action '$action' is not defined for collections.");
        }
    }
    
    protected function dispatchObject(string $action, $additional = null, $additional2 = null)
    {
        switch ($action) {
            case 'load':
                return $this->dispatchToAction(MysqlObjectLoad::class, $additional);
                break;
            case 'store':
                return $this->dispatchToAction(MysqlObjectStore::class, $additional);
                break;
            case 'update':
                return $this->dispatchToAction(MysqlObjectUpdate::class, $additional);
                break;
            case 'delete':    
                return $this->dispatchToAction(MysqlObjectDelete::class, $additional);
                break;
            case 'drop':
                return $this->dispatchToAction(MysqlObjectDrop::class);
                break;
            case 'search':
                return $this->dispatchToAction(MysqlObjectSearch::class);
                break;
            case 'degrade':
                return $this->dispatchToAction(MysqlObjectDegrade::class, $additional);
                break;
            case 'promote':
                return $this->dispatchToAction(MysqlObjectPromote::class, $additional, $additional2);
                break;
            case 'migrate':
                return $this->dispatchToAction(MysqlObjectMigrate::class);
                break;
            default:
                throw new ActionNotFoundException("The action '$action' is not defined for objects.");
        }        
    }

    protected function dispatchOther(string $action, $additional = null, $additional2 = null)
    {
        switch ($action) {
            case 'tags':
                return $this->dispatchToAction(MysqlTagSearch::class);
                break;
            case 'attributes':
                return $this->dispatchToAction(MysqlAttributeAction::class);
                break;
            default:
                throw new ActionNotFoundException("The action '$action' is unhandled.");
        }
    }
    
    public function IDExists($id): bool
    {
        $query = DB::table(($this->collection)::getInfo('table'))->where('id',$id)->first();
        return !empty($query);
    }
    
}
