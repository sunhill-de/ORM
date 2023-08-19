<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\CalcClass;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Storage\Mysql\Utils\ColumnInfo;

class MigrateFreshTest extends DatabaseTestCase
{
    
    use ColumnInfo, TableAssertions;
    
    protected function checkTable($table, $fields)
    {
        $this->assertDatabaseHasTable($table, "Expected table $table doesnt exist.");
        $table_fields = Schema::getColumnListing($table);
        foreach ($fields as $field) {
            if (!in_array($field,$table_fields)) {
                $this->fail("'$field' is not in database");
            }
        }
        foreach ($table_fields as $field) {
            if (!in_array($field,$fields)) {
                $this->fail("'$field' is in database but not expected");
            }
        }        
    }
    
    /**
     * @dataProvider droppedTableProvider
     * @param unknown $class
     * @param unknown $tables
     */
    public function testDroppedTable($class, $tables)
    {
        foreach ($tables as $table => $field) {
            Schema::drop($table);
            $this->assertDatabaseHasNotTable($table, "Unexpected table $table exists.");
        }
        
        $object = new $class();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        foreach ($tables as $table => $field) {
            $this->checkTable($table, $field);
        }
    }
    
    public static function droppedTableProvider()
    {
        return [
            [Dummy::class, ['dummies'=>['id','dummyint']]],
            [TestParent::class, [
                'testparents'=>['id','parentint','parentchar','parentfloat',
                'parentenum','parenttext','parentdate',
                'parentdatetime','parenttime','parentobject',
                'nosearch','parentcalc'],
                'testparents_parentoarray'=>['id','value','index'],
                'testparents_parentsarray'=>['id','value','index'],
            ]],
            [TestChild::class, [
                'testchildren'=>['id','childint','childchar','childfloat',
                'childenum','childtext','childdate',
                'childdatetime','childtime','childobject','childcalc'],
                'testchildren_childoarray'=>['id','value','index'],
                'testchildren_childsarray'=>['id','value','index'],
            ]],
            [CalcClass::class,[
                'calcclasses'=>['id','dummyint','calcfield','calcfield2'],
            ]],
            [TestSimpleChild::class,[
                'testsimplechildren'=>['id'],
            ]],
            [ReferenceOnly::class, [
                'referenceonlies'=>['id'],
                'referenceonlies_testoarray'=>['id','value','index'],
                'referenceonlies_testsarray'=>['id','value','index'],
            ]],            
        ];    
    }
    
    public function testDefaultValue()
    {
        Schema::drop('dummychildren');
        $object = new DummyChild();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        DB::table('dummychildren')->insert(['id'=>100]);
        $query = DB::table('dummychildren')->where('id',100)->first();
        $this->assertEquals(33, $query->dummychildint);        
    }
        
}