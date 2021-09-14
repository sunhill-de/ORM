<?php
namespace Sunhill\ORM\Tests\Unit\Console;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facade\Classes;

class ORMChecksTest extends TestCase
{

    public function testFacadeIsCalled() {
        Classes::shouldReceive('registerClass')->atLeast()->times(1);
        Artisan::call('sunhill:migrate');    
    }
  
}
