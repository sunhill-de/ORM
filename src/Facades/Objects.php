<?php

namespace Sunhill\ORM\Facades;

use Illuminate\Support\Facades\Facade;

class Objects extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'objects';
    }
}
