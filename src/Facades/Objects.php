<?php

/**
 * @file Objects.php
 * A facade to the objects_manager
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\ORM\Facades;

use Illuminate\Support\Facades\Facade;

class Objects extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'objects';
    }
}
