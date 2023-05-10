<?php
/**
 * @file InteractsWithStorage.php
 * Defines an interface that is able to interact with a storage
 * Lang en
 * Reviewstatus: 2023-05-08
 * Localization: complete
 * Documentation: complete
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;

interface Commitable 
{

    public function commit();
    public function rollback();
    
}