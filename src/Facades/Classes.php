<?php

namespace Sunhill\ORM\Facades;

use Illuminate\Support\Facades\Facade;

class Classes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'classes';
    }
}
