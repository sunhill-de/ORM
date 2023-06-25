<?php
/**
 * @file Cachable.php
 * Defines the trait that makes a property cachable
 * Lang en
 * Reviewstatus: 2023-06-14
 * Localization: complete
 * Documentation: complete
 */

namespace Sunhill\ORM\Properties\Utils;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\ORMObject;

define('CACHE_ASAP', 0);
define('CACHE_MINUTE', 60);
define('CACHE_HOUR', 3600);
define('CACHE_DAY', 3600*24);

trait Cachable 
{

    protected $max_age = CACHE_ASAP;
    
    protected $last_update = 0;
    
    /**
     * Cached fields are always initialized
     */
    protected function initializeValue(): bool
    {
        return true;
    }
    
    public function setMaximumAge(int $maximum_age)
    {
        $this->max_age = $maximum_age;
        return $this;
    }
    
    public function getMaximumAge(): int
    {
        return $this->max_age;
    }
    
    protected function isCacheValid(): bool
    {
        return (time() - $this->last_update) < $this->max_age;
    }
    
    public function invalidateCache()
    {
        $this->last_update = 0;
    }
    
    protected function cacheUpdated()
    {
        $this->last_update = time();
    }
    
    protected function updateCache()
    {
        $this->loadValue($this->retrieveValue());
        $this->cacheUpdated();    
    }
    
    protected function getValueFromCache()
    {
        if (!$this->isCacheValid()) {
            $this->updateCache();
        }
        return $this->getValue();
    }
    
    protected function &doGetValue()
    {
        return $this->getValueFromCache();
    }
    
}