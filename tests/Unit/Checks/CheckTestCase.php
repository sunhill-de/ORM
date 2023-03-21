<?php

/**
 * @file tests/Unit/Checks/CheckTestCase.php
 * Basic testcase for check tests
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Sunhill\Basic\Checker\CheckException;
use Sunhill\Basic\Checker\Checker;
use Sunhill\ORM\Tests\DatabaseTestCase;

class CheckTestCase extends DatabaseTestCase 
{
    
    protected function runCheck(Checker $checker, string $method, bool $repair)
    {
        try {
            $checker->$method($repair);
        } catch (CheckException $e) {
            
        }
        return $checker->getLastResult();
    }
        
}
