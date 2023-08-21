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

class MigrateDropColumnTest extends DatabaseTestCase
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
            
        $test->setCollection($object);
        $test->dispatch('migrate');
        
        $this->assertDatabaseTableHasNotColumn('dummies','dropped');
    }
    
    public function testDroppedSArryColumn()
    {
        Schema::create('dummies_sarray', function($table) {
            $table->integer('id')->primary();
            $table->char('target');
            $table->integer('index');
        });
            
        $object = new Dummy();
        $test = new MysqlStorage($object);
            
        $test->setCollection($object);
        $test->dispatch('migrate');
        
        $this->assertDatabaseHasNotTable('dummies_sarray');
    }

    public function testDroppedOArryColumn()
    {
        Schema::create('dummies_oarray', function($table) {
            $table->integer('id')->primary();
            $table->char('target');
            $table->integer('index');
        });
            
            $object = new Dummy();
            $test = new MysqlStorage($object);
            
            $test->setCollection($object);
            $test->dispatch('migrate');
            
            $this->assertDatabaseHasNotTable('dummies_oarray');
    }
    
}