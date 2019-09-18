<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

class sunhill_testcase_db extends TestCase {
    
    /**
     * Speichert die Namen der Tabllen, die für den Betrieb des Frameworks benötigt werden
     * @var array
     */
    protected $system_tables = ['attributes','attributevalues','caching','externalhooks','objects','objectobjectassigns',
        'stringobjectassigns','tagcache','tagobjectassigns','tags'];
    
    /**
     * Speichert die Namen der Tabellen, die ansonsten benötigt werden und nicht zu Testzwecken erzeugt wurden
     * @var array
     */
    protected $reserved_tables = ['migrations'];
    
    protected function prepare_tables() {
        $this->clear_system_tables();
        $this->DropTestClasses();
    }
    
    protected function create_special_table(string $name) {
        $special_tables = ['dummies'=>['dummyint int'],
                           'referenceonlies'=>['testint int'],
                           'testparents'=>[ 'parentint int',
                                            'parentchar varchar(255)',
                                            'parentfloat float',
                                            'parenttext text',
                                            'parentdatetime datetime', 
                                            'parentdate date', 
                                            'parenttime time',
                                            "parentenum ENUM('testA','testB','testC')"],
                            'testchildren'=>['childint int',
                                            'childchar varchar(255)',
                                            'childfloat float',
                                            'childtext text',
                                            'childdatetime datetime',
                                            'childdate date',
                                            'childtime time',
                                            "childenum ENUM('testA','testB','testC')"],
                            'passthrus'=>[],
                            'secondlevelchildren'=>['childint int'],
                            'thirdlevelchildren'=>['childchildint int'],
                        ];    
        if (isset($special_tables[$name])) {
            $this->create_table($name,$special_tables[$name]);
        } else {
            die("Tabelle '$name' nicht gefunden.");
        }
    }
    
    protected function create_table(string $name,array $descriptor) {
        $querystr = 'create table '.$name.' (id int primary key';
        foreach ($descriptor as $entry) {
            $querystr .= ','.$entry;
        }
        $querystr .= ')';
        DB::statement($querystr);
    }
    
    /**
     * Löscht alle Tabellen, die keine Systemtabellen oder reservierte Tabellen sind
     */
    protected function DropTestClasses() {
        $tables = DB::select('SHOW TABLES');
        foreach($tables as $table_info)
        {
            $table  = $table_info->Tables_in_sunhill;
            if (!in_array($table,$this->system_tables) && (!in_array($table,$this->reserved_tables))) {
                DB::statement("drop table ".$table);
            }
        }
        \Sunhill\Objects\oo_object::flush_cache();
    }
    
    /** 
     * Leert die Systemtabellen
     */
    protected function clear_system_tables() {
        foreach ($this->system_tables as $table) {
            DB::statement("truncate ".$table);
        }
    }
    
    /**
     * Fügt in die Tabelle mit dem Namen '$name' und den Feldern '$fields' die Werte 'values' ein
     * @param string $name
     * @param array $fields
     * @param array $values
     */
    protected function insert_into(string $name,array $fields,array $values) {
        $querystr = 'insert into '.$name.' (';
        $first = true;
        foreach ($fields as $field) {
            if (!$first) {
                $querystr .= ',';
            }
            $querystr .= "`".$field."`";
            $first = false;
        }
        $querystr .= ') values ';
        $firstset = true;
        foreach ($values as $valueset) {
            if (!$firstset) {
                $querystr .= ',';
            }
            $firstset = false;
            $querystr .= '(';
            $first = true;
            foreach ($valueset as $value) {
                if (!$first) {
                    $querystr .= ',';
                }
                $value = DB::connection()->getPdo()->quote($value);
                $querystr .= $value;
                $first = false;
            }
            $querystr .= ')';
        }
        DB::statement($querystr);
    }
    
    protected function seed() {
        exec(dirname(__FILE__).'/../../application db:seed');
    }
}