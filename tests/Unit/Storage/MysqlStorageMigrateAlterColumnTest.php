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
    
    /**
     * @dataProvider ChangeAColumnProvider
     */
    public function testChangeAColumnType($column, $type, $expect)
    {
        Schema::table('testparents', function($table) use ($column, $type) {
            $table->dropColumn($column);
        });
        Schema::table('testparents', function($table) use ($column, $type) {
            $table->$type($column)->nullable()->default(null);
        });
                
            $object = new TestParent();
            $test = new MysqlStorage($object);
            
            $test->migrate();
            
            $this->assertEquals($expect,$this->getColumnType('testparents', $column));            
    }

    public function ChangeAColumnProvider()
    {
        return [
            ['parentchar','integer','string'],
            ['parentint','string','integer'],
            ['parentfloat','integer','float'],
            ['parentdate','integer','date'],
            ['parentdatetime','integer','datetime'],
            ['parenttime','integer','time'],
        ];    
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

    public function testEnumNewValue()
    {
        Schema::table('testparents', function($table)  {
            $table->dropColumn('parentenum');
        });
        Schema::table('testparents', function($table)  {
            $table->enum('parentenum', ['testA','testB','testC','testD'])->default('testA');
        });
                
        $object = new TestParent();
        $test = new MysqlStorage($object);
                
        $test->migrate();
        
        $enum_values = $this->getEnumValue('testparents', 'parentenum');
        $this->assertEquals(['testA','testB','testC'], $enum_values);
                
    }

    public function testEnumDropedValue()
    {
        Schema::table('testparents', function($table) {
            $table->dropColumn('parentenum');
        });
        Schema::table('testparents', function($table) {
            $table->enum('parentenum',['testA','testB'])->default('testA');
        });
                
        $object = new TestParent();
        $test = new MysqlStorage($object);
                
        $test->migrate();
                
        $enum_values = $this->getEnumValue('testparents', 'parentenum');
        $this->assertEquals(['testA','testB','testC'], $enum_values);
    }
}