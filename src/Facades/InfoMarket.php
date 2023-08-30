<?php
/**
 * @file InfoMarket.php
 * Provides the facade to the InfoMarket
 * Lang en
 * Reviewstatus: 2021-10-26
 * Localization: none
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\ORM\Facades;

use Illuminate\Support\Facades\Facade;

class InfoMarket extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'infomarket';
    }
}
