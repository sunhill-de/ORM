<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Storage\Mysql\ColumnInfo;

class MysqlStorageMigrateAlterColumnTest extends DatabaseTestCase
{
    
    use ColumnInfo;
    
    public function testChangedColumnType()
    {
        Schema::dropIfExists('testparents');
        Schema::create('testparents', function($table) {
            $table->integer('parentchar')->nullable();
            $table->integer('parentint');
            $table->float('parentfloat');
            $table->text('parenttext');
            $table->datetime('parentdatetime');
            $table->date('parentdate');
            $table->time('parenttime');
            $table->enum('parentenum',['testA','testB','testC']);
            $table->integer('parentobject')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1);
        });
            
       $object = new TestParent();
       $test = new MysqlStorage($object);
            
       $test->migrate();
            
       $this->assertEquals('string',$this->getColumnType('testparents', 'parentchar'));            
    }
    
    public function testChangedColumnDefault()
    {
        Schema::dropIfExists('testparents');
        Schema::create('testparents', function($table) {
            $table->char('parentchar')->nullable();
            $table->integer('parentint');
            $table->float('parentfloat');
            $table->text('parenttext');
            $table->datetime('parentdatetime');
            $table->date('parentdate');
            $table->time('parenttime');
            $table->enum('parentenum',['testA','testB','testC']);
            $table->integer('parentobject')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1111);
        });
            
        $object = new TestParent();
        $test = new MysqlStorage($object);
            
        $test->migrate();
            
        $this->assertEquals(1,$this->getColumnDefault('testparents', 'nosearch'));            
    }

    
    public function testChangedColumnDefaultNull()
    {
        Schema::dropIfExists('testparents');
        Schema::create('testparents', function($table) {
            $table->char('parentchar');
            $table->integer('parentint');
            $table->float('parentfloat');
            $table->text('parenttext');
            $table->datetime('parentdatetime');
            $table->date('parentdate');
            $table->time('parenttime');
            $table->enum('parentenum',['testA','testB','testC']);
            $table->integer('parentobject')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1111);
        });
            
            $object = new TestParent();
            $test = new MysqlStorage($object);
            
            $this->assertFalse($this->getColumnDefaultsNull('testparents', 'parentchar'));
            
            $test->migrate();
            
            $this->assertTrue($this->getColumnDefaultsNull('testparents', 'parentchar'));
    }
    
}