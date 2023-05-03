<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Storage\Mysql\ColumnInfo;

class MysqlStorageMigrateAddColumnTest extends DatabaseTestCase
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
        ];    
    }
    
    public function testAddSArrayColumn()
    {
        Schema::dropIfExists('testparents_array_parentsarray');
            // parentint is missing !
            
            $object = new TestParent();
            $test = new MysqlStorage($object);
            
            $test->migrate();
            
            $this->assertDatabaseHasTable('testparents_array_parentsarray');            
    }
    
    public function testAddOArrayColumn()
    {
        Schema::dropIfExists('testparents_array_parentoarray');
        // parentint is missing !
        
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        
        $this->assertDatabaseHasTable('testparents_array_parentoarray');        
    }
    
    public function testAddCalcColumn()
    {
        Schema::dropIfExists('testparents_calc_parentcalc');
        // parentint is missing !
        
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        
        $this->assertDatabaseHasTable('testparents_calc_parentcalc');        
    }
        
}