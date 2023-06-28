<?php

/**
 * @file ObjectData.php
 * A facade to the ObjectDataGenerator
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-26
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\ORM\Facades;

use Illuminate\Support\Facades\Facade;

class ObjectData extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'objectdata';
    }
}
