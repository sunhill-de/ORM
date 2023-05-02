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

class MysqlStorageMigrateDropColumnTest extends DatabaseTestCase
{
    
    use ColumnInfo, TableAssertions;
    
    public function testDroppedSimpleColumn()
    {
        Schema::drop('dummies');
        Schema::create('dummies', function($table) {
            $table->integer('id')->primary();
            $table->integer('dummyint');
            $table->integer('dropped');
        });

        $object = new Dummy();
        $test = new MysqlStorage($object);
            
        $test->migrate();
        
        $this->assertDatabaseTableHasNotColumn('dummies','dropped');
    }
    
    public function testDroppedSArryColumn()
    {
        Schema::create('dummies_array_sarray', function($table) {
            $table->integer('id')->primary();
            $table->char('target');
            $table->integer('index');
        });
            
        $object = new Dummy();
        $test = new MysqlStorage($object);
            
        $test->migrate();
            
        $this->assertDatabaseHasNotTable('dummies_array_sarray');
    }

    public function testDroppedOArryColumn()
    {
        Schema::create('dummies_array_oarray', function($table) {
            $table->integer('id')->primary();
            $table->char('target');
            $table->integer('index');
        });
            
            $object = new Dummy();
            $test = new MysqlStorage($object);
            
            $test->migrate();
            
            $this->assertDatabaseHasNotTable('dummies_array_oarray');
    }
    
    public function testDroppedCalcColumn()
    {
        Schema::create('dummies_calc_calc', function($table) {
            $table->integer('id')->primary();
            $table->char('value');
        });
            
            $object = new Dummy();
            $test = new MysqlStorage($object);
            
            $test->migrate();
            
            $this->assertDatabaseHasNotTable('dummies_calc_calc');
    }
        
}