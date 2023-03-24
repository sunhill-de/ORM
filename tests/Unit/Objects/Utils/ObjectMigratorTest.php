<?php

namespace Sunhill\ORM\tests\Unit\Objects\Utils;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Objects\Utils\ObjectMigrator;

class ObjectMigratorTest extends DatabaseTestCase
{
    /**
     * tests: ObjectMigrator::storeInformations
     */
    public function testStoreInformations()
    {
        $test = new ObjectMigrator();
        $this->callProtectedMethod($test, 'storeInformations', ['dummy']);
        $this->assertEquals(Dummy::class, $this->getProtectedProperty($test, 'class_namespace'));
        $this->assertEquals('dummies', $this->getProtectedProperty($test, 'class_tablename'));
    }
    
    /**
     * Tests: ObjectMigrator::tableExists
     */
    public function testTableExists()
    {
        $test = new ObjectMigrator();
        $this->setProtectedProperty($test, 'class_tablename', 'objects');
        $this->assertTrue($this->callProtectedMethod($test, 'tableExists'));        
        $this->setProtectedProperty($test, 'class_tablename', 'notexisting');
        $this->assertFalse($this->callProtectedMethod($test, 'tableExists'));
    }
}
