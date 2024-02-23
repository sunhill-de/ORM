<?php

namespace Sunhill\ORM\Tests\TestSupport;

class TestUserManager
{
    
    public static function hasCapability(string $capability): bool
    {
        return ($capability == 'required');
    }
    
}