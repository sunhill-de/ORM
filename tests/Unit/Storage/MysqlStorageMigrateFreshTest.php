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
use Sunhill\ORM\Storage\Mysql\ColumnInfo;

class MysqlStorageMigrateFreshTest extends DatabaseTestCase
{
    
    use ColumnInfo, TableAssertions;
    
    protected function checkTable($table, $fields)
    {
        $this->assertDatabaseHasTable($table);
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
            $this->assertDatabaseHasNotTable($table);
        }
        
        $object = new $class();
        $test = new MysqlStorage($object);
        
        $test->migrate();
        foreach ($tables as $table => $field) {
            $this->checkTable($table, $field);
        }
    }
    
    public function droppedTableProvider()
    {
        return [
            [Dummy::class, ['dummies'=>['id','dummyint']]],
            [TestParent::class, [
                'testparents'=>['id','parentint','parentchar','parentfloat',
                'parentenum','parenttext','parentdate',
                'parentdatetime','parenttime','parentobject',
                'nosearch'],
                'testparents_array_parentoarray'=>['id','target','index'],
                'testparents_array_parentsarray'=>['id','target','index'],
                'testparents_calc_parentcalc'=>['id','value']]],
            [TestChild::class, [
                'testchildren'=>['id','childint','childchar','childfloat',
                'childenum','childtext','childdate',
                'childdatetime','childtime','childobject'],
                'testchildren_array_childoarray'=>['id','target','index'],
                'testchildren_array_childsarray'=>['id','target','index'],
                'testchildren_calc_childcalc'=>['id','value']
            ]],
            [CalcClass::class,[
                'calcclasses'=>['id','dummyint'],
                'calcclasses_calc_calcfield'=>['id','value'],
                'calcclasses_calc_calcfield2'=>['id','value'],
            ]],
            [TestSimpleChild::class,[
                'testsimplechildren'=>['id'],
            ]],
            [ReferenceOnly::class, [
                'referenceonlies'=>['id'],
                'referenceonlies_array_testoarray'=>['id','target','index'],
                'referenceonlies_array_testsarray'=>['id','target','index'],
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