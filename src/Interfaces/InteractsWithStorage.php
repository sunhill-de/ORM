<?php
/**
 * @file InteractsWithStorage.php
 * Defines an interface that is able to interact with a storage
 * Lang en
 * Reviewstatus: 2023-05-08
 * Localization: complete
 * Documentation: complete
 */

namespace Sunhill\ORM\Interfaces;

use Sunhill\ORM\Storage\StorageBase;

interface InteractsWithStorage 
{

    public function storeToStorage(StorageBase $storage);
    public function updateToStorage(StorageBase $storage);
    public function loadFromStorage(StorageBase $storage);
    
}