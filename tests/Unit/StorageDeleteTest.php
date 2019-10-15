<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class StorageDeleteTest extends StorageBase
{
    
    /**
     * @dataProvider DeleteProvider
     * @group delete
     * @param unknown $id
     * @param unknown $class
     * @param unknown $table
     * @param unknown $field
     */
    public function testDelete($id,$class,$table,$field) {
        $this->prepare_read();
        $object = new $class();
        $changer = new \Sunhill\Storage\storage_mysql($object);
        $before = empty(DB::table($table)->where($field,$id)->first());
        $changer->delete_object($id);        
        $after = empty(DB::table($table)->where($field,$id)->first());
        $this->assertEquals($before,!$after);
    }
    
    public function DeleteProvider() {
        return [
            [1,'Sunhill\\Test\\ts_dummy','dummies','id'],                       // Wird ein einfaches Feld gelesen?
            [1,'Sunhill\\Test\\ts_dummy','objects','id'],                       // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\Test\\ts_dummy','dummies','id'],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\Test\\ts_dummy','tagobjectassigns','container_id'],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy','attributevalues','object_id'],      // Werden Attribute richtig ausgelesen
            
            [5,'Sunhill\\Test\\ts_testparent','testparents','id'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','caching','object_id'],             // Werden calculierte Felder gelesen
            [5,'Sunhill\\Test\\ts_testparent','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','objects','id'],            // Werden Objektarrays gelesen
            [5,'Sunhill\\Test\\ts_testparent','attributevalues','object_id'],    // Werden Attribute gelesen
            
            
            [6,'Sunhill\\Test\\ts_testchild','testchildren','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','testparents','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','objects','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','objects','id'],            // Werden Objektarrays gelesen
            [6,'Sunhill\\Test\\ts_testchild','attributevalues','object_id'],    // Werden Attribute gelesen
                        
            [7,'Sunhill\\Test\\ts_passthru','passthrus','id']                         // Werden Objekte ohne Simple-Fields geladen
        ];        
    }
}
