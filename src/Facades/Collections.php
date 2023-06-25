<?php

/**
 * @file Collections.php
 * A facade to the CollectionManager
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-25
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\ORM\Facades;

use Illuminate\Support\Facades\Facade;

class Collections extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'collections';
    }
}
