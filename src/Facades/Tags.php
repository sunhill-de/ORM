<?php

/**
 * @file Tag.php
 * A facade to the TagManager
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

class Tags extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tags';
    }
}
