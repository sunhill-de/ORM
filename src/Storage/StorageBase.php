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
use Sunhill\ORM\Storage\Exceptions\ActionNotFoundException;

/**
 * 
 * @author lokal
 *
 */
abstract class StorageBase  
{
    
    protected $collection;
    
    public function setCollection(PropertiesCollection $collection)
    {
        $this->collection = $collection;    
    }
    
    public function getCollection(): ?PropertiesCollection
    {
        return $this->collection;    
    }

    abstract public function dispatch(string $action, $additional = null, $additional2 = null);
     
    protected function dispatchToAction(string $storage_action, $additional = null, $additional2 = null)
    {
        if (!class_exists($storage_action, true)) {
            throw new ActionNotFoundException("The action '$storage_action' is not found.");
        }
        $action = new $storage_action();
        if (!is_null($additional)) {
            $action->setAdditional($additional);
        }
        if (!is_null($additional2)) {
            $action->setAdditional2($additional2);
        }
        if ($this->collection) {
            $action->setCollection($this->collection);
        }
        return $action->run();
    }
    
    abstract public function IDExists($id): bool;
}
