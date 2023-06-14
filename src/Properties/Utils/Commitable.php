<?php
/**
 * @file InteractsWithStorage.php
 * Defines an interface that is able to interact with a storage
 * Lang en
 * Reviewstatus: 2023-06-14
 * Localization: complete
 * Documentation: complete
 */

namespace Sunhill\ORM\Properties\Utils;

use Sunhill\ORM\Storage\StorageBase;

interface Commitable 
{

    public function commit();
    public function rollback();
    
}