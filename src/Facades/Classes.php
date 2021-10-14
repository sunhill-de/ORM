<?php

/**
 * @file Classes.php
 * A facade to the ClassManager
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

class Classes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'classes';
    }
}
