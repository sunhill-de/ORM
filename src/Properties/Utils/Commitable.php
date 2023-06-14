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

interface Commitable 
{

    public function commit();
    public function rollback();
    
}