<?php

/**
 * @file ClassQuery.php
 * Provides the ClassQuery for querying classes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-23
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Query\ArrayQuery;

class ClassQuery extends ArrayQuery
{
    
    protected function getRawData()
    {
        return Classes::getAllClasses();
    }
    
}