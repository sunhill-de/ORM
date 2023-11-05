<?php

/**
 * @file ManagerBase.php
 * Provides a base for managers 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-11-05
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */

namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Query\BasicQuery;

abstract class ManagerBase
{
    
    abstract public function query(): BasicQuery;
    
}