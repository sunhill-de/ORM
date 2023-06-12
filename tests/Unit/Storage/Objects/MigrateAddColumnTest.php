<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Storage\Mysql\Utils\ColumnInfo;

class MigrateAddColumnTest extends DatabaseTestCase
{
    
    use ColumnInfo, TableAssertions;
    
    /**
     * @dataProvider AddASimpleColumnProvider
     * @param unknown $table
     * @param unknown $column
     */
    public function testAddASimpleColumn($column)
    {
        Schema::table('testparents', function ($dbobject) use ($column) {
            $dbobject->dropColumn($column);
        });
        $this->assertDatabaseTableHasNotColumn('testparents', $column);
        
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        
        $this->assertDatabaseTableHasColumn('testparents', $column);
    }

    public function AddASimpleColumnProvider()
    {
        return [
            ['parentchar'],
            ['parentint'],
            ['parentfloat'],
            ['parentdate'],
            ['parentdatetime'],
            ['parenttime'],
            ['parentenum'],
            ['parentobject'],
            ['parentcalc'],
        ];    
    }
    
    public function testAddSArrayColumn()
    {
        Schema::dropIfExists('testparents_parentsarray');
            // parentint is missing !
            
            $object = new TestParent();
            $test = new MysqlStorage($object);
            
            $test->migrate();
            
            $this->assertDatabaseHasTable('testparents_parentsarray');            
    }
    
    public function testAddOArrayColumn()
    {
        Schema::dropIfExists('testparents_parentoarray');
        // parentint is missing !
        
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        
        $this->assertDatabaseHasTable('testparents_parentoarray');        
    }
    
        
}