<?php

/**
 * @file Attributes.php
 * A facade to the AttributeManager
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

class Attributes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'attributes';
    }
}
