<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Illuminate\Support\Facades\Schema;

trait TableAssertions
{
    public function assertDatabaseTableHasColumn($table, $column)
    {
        $table_fields = Schema::getColumnListing($table);
        $this->assertTrue(in_array($column, $table_fields));
    }
    
    public function assertDatabaseTableHasNotColumn($table, $column)
    {
        $table_fields = Schema::getColumnListing($table);
        $this->assertFalse(in_array($column, $table_fields));
    }
        
}

