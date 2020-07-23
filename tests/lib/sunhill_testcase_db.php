<?php

namespace Sunhill\Test;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

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
    
    protected function create_write_scenario() {
        $this->insert_into('tags',['id','created_at','updated_at','name','options','parent_id'],
            [
                [1,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagA',0,0],
                [2,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagB',0,0],
                [3,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagC',0,2],
                [4,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagD',0,0],
                [5,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagE',0,0],
                [6,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagF',0,0],
            ]);
        $this->insert_into('tagcache',['id','name','tag_id','created_at','updated_at'],
            [
                [1,'TagA',1,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,'TagB',2,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,'TagC',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,'TagC.TagB',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [5,'TagD',4,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,'TagE',5,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,'TagF',6,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('attributes',['id','name','type','allowedobjects','property'],
            [
                [1,'int_attribute','int',"\\Sunhill\\Test\\ts_dummy",''],
                [2,'attribute1','int',"\\Sunhill\\Test\\ts_testparent",''],
                [3,'attribute2','int',"\\Sunhill\\Test\\ts_testparent",''],
                [4,'general_attribute','int',"\\Sunhill\\Objects\\oo_object",''],
                
            ]);        
    }
    
    protected function create_load_scenario() {
        $this->create_write_scenario();
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [
                [1,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [5,"\\Sunhill\\Test\\ts_testparent",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,"\\Sunhill\\Test\\ts_testchild",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,"\\Sunhill\\Test\\ts_passthru",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('dummies',['id','dummyint'],[[1,123],[2,234],[3,345],[4,456]]);
        $this->insert_into('testparents',['id','parentint','parentchar','parentfloat','parenttext','parentdatetime',
            'parentdate','parenttime','parentenum'],
            [
                [5,123,'ABC',1.23,'Lorem ipsum','1974-09-15 17:45:00','1978-06-05','01:11:00','testC'],
                [6,234,'DEF',2.34,'Upsala Dupsala','1970-09-11 18:00:00','2013-11-24','16:00:00','testB'],
                [7,321,'FED',4.32,'Ups Dup','1970-09-11 18:00:00','2013-11-24','16:00:00','testB']
            ]);
        $this->insert_into('testchildren',['id','childint','childchar','childfloat','childtext','childdatetime',
            'childdate','childtime','childenum'],
            [
                [6,345,'GHI',3.45,'Norem Torem','1973-01-24 18:00:00','2016-06-17','18:00:00','testA']
            ]);
        $this->insert_into('passthrus',['id'],[[7]]);
        $this->insert_into('tagobjectassigns',['container_id','tag_id'],
            [
                [1,1],[1,2]
            ]);
        $this->insert_into('objectobjectassigns',['container_id','element_id','field','index'],
            [
                [5,1,'parentobject',0], // parent->parentobject = dummy(1)
                [5,2,'parentoarray',0], // parent->parentoarray[0] = dummy(2)
                [5,3,'parentoarray',1], // parent->parentoarray[1] = dummy(3)
                
                [6,3,'parentobject',0],
                [6,1,'parentoarray',0],
                [6,2,'parentoarray',1],
                [6,2,'childobject',0],
                [6,3,'childoarray',0],
                [6,4,'childoarray',1],
                [6,1,'childoarray',2],
                                
            ]);
        $this->insert_into('stringobjectassigns',['container_id','element_id','field','index'],
            [
                [5,'ObjectString0','parentsarray',0],
                [5,'ObjectString1','parentsarray',1],
                [6,'Parent0','parentsarray',0],
                [6,'Parent1','parentsarray',1],
                [6,'Child0','childsarray',0],
                [6,'Child1','childsarray',1],
                [6,'Child2','childsarray',2],
            ]);
        $this->insert_into('caching',['id','object_id','fieldname','value'],
            [
                [1,5,'parentcalc','123A'],
                [2,6,'parentcalc','234A'],
            ]);
        $this->insert_into('attributevalues',['id','attribute_id','object_id','value','textvalue','created_at','updated_at'],
            [
                [1,1,1,111,'','2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,2,5,121,'','2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,2,6,232,'','2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,3,6,666,'','2019-05-15 10:00:00','2019-05-15 10:00:00']
            ]);  
        $this->insert_into('externalhooks',['id','container_id','target_id','action','subaction','hook','payload'],
            [
                [1,1,2,'PROPERTY_UPDATED','dummyint','dummyint_updated',null],
                [2,2,1,'PROPERTY_UPDATED','dummyint','dummyint2_updated',null],
                [3,1,5,'PROPERTY_UPDATED','dummyint','dummyint3_updated',null]
            ]);
    }
    
}