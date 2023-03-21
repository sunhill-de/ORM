<?php
/**
 * @file TestSimpleChild.php
 * Provides the test object TestSimpleChild has no own properties
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Testobjects;

class TestSimpleChild extends TestParent {

    protected static function setupInfos()
    {
        static::addInfo('name', 'testsimplechild');
        static::addInfo('table', 'testsimplechildren');
        static::addInfo('name_s', 'testsimplechild');
        static::addInfo('name_p', 'testsimplechild');
        static::addInfo('description', 'Another test class. A class with no own properties');
        static::addInfo('options', 0);
    }
    
}

