<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Storage\Mysql\Utils\ColumnInfo;
use Sunhill\ORM\Properties\Utils\DefaultNull;

class MigrateTest extends DatabaseTestCase
{
    
    use ColumnInfo;
    
    /**
     * @group migratecollection
     * @group collection
     * @group migrate
     */
    public function testDummyCollectionFresh()
    {
        Schema::drop('dummycollections');
        
        $collection = new DummyCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseMissingTable('dummycollections');
        
        $test->dispatch('migrate');
        
        $this->assertDatabaseHasTable('dummycollections');
        $this->assertTrue($this->columnExists('dummycollections', 'dummyint'));
        $this->assertEquals('integer',$this->getColumnType('dummycollections', 'dummyint'));
    }
    
    /**
     * @group migratecollection
     * @group collection
     * @group migrate
     */
    public function testComplexCollectionFresh()
    {
        Schema::drop('complexcollections');
        Schema::drop('complexcollections_field_sarray');
        Schema::drop('complexcollections_field_oarray');
        Schema::drop('complexcollections_field_smap');
        
        $collection = new ComplexCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseMissingTable('complexcollections');
        $this->assertDatabaseMissingTable('complexcollections_field_sarray');
        $this->assertDatabaseMissingTable('complexcollections_field_oarray');
        $this->assertDatabaseMissingTable('complexcollections_field_smap');
        
        $test->dispatch('migrate');
        
        $this->assertDatabaseHasTable('complexcollections');
        $this->assertDatabaseHasTable('complexcollections_field_sarray');
        $this->assertDatabaseHasTable('complexcollections_field_oarray');
        $this->assertDatabaseHasTable('complexcollections_field_smap');
    }
    
    /**
     * @group migratecollection
     * @group migrate
     * @group collection
     */
    public function testCollectionDropSimpleColumn()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->integer('dropfield');
            $table->boolean('field_bool');
            $table->char('field_char',10)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object')->nullable()->default(null);
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1);
            $table->string('field_calc');
        });
        $collection = new ComplexCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $test->dispatch('migrate');
        
        $this->assertFalse($this->columnExists('complexcollections','dropfield'));            
    }
    
    /**
     * @group migratecollection
     * @group migrate
     * @group collection
     */
    public function testCollectionDropArrayColumn()
    {
        Schema::create('complexcollections_droparray', function($table) {
            $table->integer('id');
            $table->integer('value');
            $table->integer('index');
            $table->primary(['id','index']);            
        });
 
        $collection = new ComplexCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
            
        $test->dispatch('migrate');
        
        $this->assertDatabaseMissingTable('complexcollections_droparray');
    }
    
    /**
     * @group migratecollection
     * @group migrate
     * @group collection
     */
    public function testCollectionAddSimpleColumn()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->char('field_char',10)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object')->nullable()->default(null);
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1);
            $table->string('field_calc');
        });
            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $test->dispatch('migrate');
            
            $this->assertTrue($this->columnExists('complexcollections','field_bool'));
            $this->assertEquals('integer',$this->getColumnType('complexcollections','field_bool'));
    }
    
    protected function checkField($table, $name, $type, $default, $maxlen)
    {
        $this->assertTrue($this->columnExists($table, $name));
        $this->assertEquals($type, $this->getColumnType($table, $name));
        if (!is_null($default)) {
            if (is_a($default, DefaultNull::class, true)) {
                $default_null = $this->getColumnDefaultsNull($table, $name);
                $this->assertTrue($this->getColumnDefaultsNull($table, $name));
            } else {
                $table_default = $this->getColumnDefault($table, $name);
                $this->assertEquals($default, $this->getColumnDefault($table, $name));
            }
        }
        if (!is_null($maxlen)) {
            $this->assertEquals($maxlen, $this->getColumnLength($table, $name));
        }
    }
    
    /**
     * @group migratecollection
     * @group migrate
     * @group collection
     */
    public function testCollectionAddAllSimpleColumns()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
        });
            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $test->dispatch('migrate');

            $this->checkField('complexcollections', 'field_int', 'integer', null, null);
            $this->checkField('complexcollections', 'field_bool', 'integer', null, null);
            $this->checkField('complexcollections', 'field_char', 'string', DefaultNull::class, 10);
            $this->checkField('complexcollections', 'field_float', 'float', null, null);
            $this->checkField('complexcollections', 'field_text', 'text', null, null);
            $this->checkField('complexcollections', 'field_datetime', 'datetime', null, null);
            $this->checkField('complexcollections', 'field_date', 'date', null, null);
            $this->checkField('complexcollections', 'field_time', 'time', null, null);
            $this->checkField('complexcollections', 'field_enum', 'string', null, null);
            $this->checkField('complexcollections', 'field_object', 'integer', null, null);
            $this->checkField('complexcollections', 'field_collection', 'integer', null, null);
            $this->checkField('complexcollections', 'field_calc', 'string', null, null);
            $this->checkField('complexcollections', 'nosearch', 'integer', 1, null);
            
    }
    
    /**
     * @group migratecollection
     * @group migrate
     * @group collection
     */
    public function testCollectionAddArrayColumn()
    {
        Schema::drop('complexcollections_field_sarray');
        
            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $test->dispatch('migrate');
            
            $this->assertDatabaseHasTable('complexcollections_field_sarray');
    }
    

    /**
     * @group migratecollection
     * @group migrate
     * @group collection
     */
    public function testCollectionChangeSimpleColumnType()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->string('field_int');
            $table->string('field_bool');
            $table->integer('field_char')->nullable();
            $table->integer('field_float');
            $table->float('field_text');
            $table->float('field_datetime');
            $table->float('field_date');
            $table->float('field_time');
            $table->integer('field_enum');
            $table->string('field_object');
            $table->string('field_collection');
            $table->string('nosearch');
            $table->integer('field_calc');
        });
            
        $collection = new ComplexCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
            
        $test->dispatch('migrate');

        $this->checkField('complexcollections', 'field_int', 'integer', null, null);
        $this->checkField('complexcollections', 'field_bool', 'integer', null, null);
        $this->checkField('complexcollections', 'field_char', 'string', DefaultNull::class, 10);
        $this->checkField('complexcollections', 'field_float', 'float', null, null);
        $this->checkField('complexcollections', 'field_text', 'text', null, null);
        $this->checkField('complexcollections', 'field_datetime', 'datetime', null, null);
        $this->checkField('complexcollections', 'field_date', 'date', null, null);
        $this->checkField('complexcollections', 'field_time', 'time', null, null);
        $this->checkField('complexcollections', 'field_enum', 'string', null, null);
        $this->checkField('complexcollections', 'field_object', 'integer', null, null);
        $this->checkField('complexcollections', 'field_collection', 'integer', null, null);
        $this->checkField('complexcollections', 'field_calc', 'string', null, null);
        $this->checkField('complexcollections', 'nosearch', 'integer', 1, null);        
    }
    
    public function testCollectionChangeArrayType()
    {
        Schema::drop('complexcollections_field_sarray');
        Schema::create('complexcollections_field_sarray', function($table) {
            $table->integer('id');
            $table->integer('value');
            $table->integer('index');
            $table->primary(['id','index']);
        });
            
        $collection = new ComplexCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
            
        $this->assertDatabaseHasTable('complexcollections_field_sarray');
        $test->dispatch('migrate');
            
        $this->assertEquals('string',$this->getColumnType('complexcollections_field_sarray','value'));            
    }
    
    public function testCollectionChangeNullable()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->boolean('field_bool');
            $table->char('field_char',10)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object')->nullable()->default(null);
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->default(1);
            $table->string('field_calc');
        });

            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $this->assertFalse($this->getColumnNullable('complexcollections', 'nosearch'));
            $test->dispatch('migrate');
            $this->assertTrue($this->getColumnNullable('complexcollections', 'nosearch'));            
    }
    
    public function testCollectionChangeMaxlenghth()
    {
        /**
         * @todo doesn't work
         */
        $this->markTestSkipped("The length of string field couldn't be retrieved.");
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->boolean('field_bool');
            $table->string('field_char',40)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object')->nullable()->default(null);
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->default(1);
            $table->string('field_calc');
        });
            
            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $this->assertEquals(40,$this->getColumnLength('complexcollections', 'field_char'));
            $test->dispatch('migrate');
            $this->assertEquals(20,$this->getColumnLength('complexcollections', 'field_char'));
    }
    
    public function testCollectionChangeDefault()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->boolean('field_bool');
            $table->char('field_char',40)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object')->nullable()->default(null);
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->default(2);
            $table->string('field_calc');
        });
            
            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $this->assertEquals(2,$this->getColumnDefault('complexcollections', 'nosearch'));
            $test->dispatch('migrate');
            $this->assertEquals(1,$this->getColumnDefault('complexcollections', 'nosearch'));
    }

    
    public function testCollectionChangeDefaultNull()
    {
        Schema::drop('complexcollections');
        Schema::create('complexcollections', function($table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->boolean('field_bool');
            $table->char('field_char',40)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object');
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->default(2);
            $table->string('field_calc');
        });
            
            $collection = new ComplexCollection();
            $test = new MysqlStorage();
            $test->setCollection($collection);
            
            $this->assertFalse($this->getColumnDefaultsNull('complexcollections', 'field_object'));
            $test->dispatch('migrate');
            $this->assertTrue($this->getColumnDefaultsNull('complexcollections', 'field_object'));
    }
    
}