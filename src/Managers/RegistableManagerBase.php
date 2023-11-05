<?php
/**
 * @file RegistableManagerBase.php
 * Provides a base for managers that make use of a registrable item (like classes, collections, etc.)
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-11-05
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Managers\Exceptions\DuplicateEntryException;

abstract class RegistableManagerBase extends ManagerBase
{
    
    protected $registered_items;
    
    public function __construct()
    {
        $this->flush();
    }
    
    /**
     * Clears the list of entries
     */
    public function flush()
    {
        $this->registered_items = [];
        $this->initialize();
    }
    
    /**
     * Initializes the list of entries
     */
    protected function initialize()
    {
        
    }
    
    protected function checkValidity($item)
    {
        
    }

    protected function getItemKey($item): string
    {
        return $item;
    }
    
    abstract protected function getItemInformation($item);
    
    public function register($item, bool $ignore_duplicates = false)
    {
        $this->checkValidity($item);
        
        $key = $this->getItemKey($item);
        if (isset($this->registered_items[$key])) {
            if (!$ignore_duplicates) {
                throw new DuplicateEntryException("The item '$key' is already registered.");
            }
        } else {
            $this->registered_items[$key] = $this->getItemInformation($item);
        }
    }
    
}

