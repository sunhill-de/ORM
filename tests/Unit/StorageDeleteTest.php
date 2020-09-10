<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\storage_mysql;

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
        $object = new $class();
        $changer = new storage_mysql($object);
        $this->assertDatabaseHas($table,[$field=>$id]);
        $changer->delete_object($id);        
        $this->assertDatabaseMissing($table,[$field=>$id]);
    }
    
    public function DeleteProvider() {
        return [
            [1,'Sunhill\\ORM\\Test\\ts_dummy','dummies','id'],                     // Wird ein einfaches Feld gelesen?
            [1,'Sunhill\\ORM\\Test\\ts_dummy','objects','id'],                     // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\ORM\\Test\\ts_dummy','dummies','id'],                     // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\ORM\\Test\\ts_dummy','tagobjectassigns','container_id'],  // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\ORM\\Test\\ts_dummy','attributevalues','object_id'],      // Werden Attribute richtig ausgelesen
            [1,'Sunhill\\ORM\\Test\\ts_dummy','externalhooks','container_id'],     // Werden Referenzen auf externe Hooks gelöscht (von $id)
            [1,'Sunhill\\ORM\\Test\\ts_dummy','externalhooks','target_id'],        // Werden Referenzen auf externe Hooks gelöscht (auf $id)
            
            [5,'Sunhill\\ORM\\Test\\ts_testparent','testparents','id'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\ORM\\Test\\ts_testparent','caching','object_id'],             // Werden calculierte Felder gelesen
            [5,'Sunhill\\ORM\\Test\\ts_testparent','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\ORM\\Test\\ts_testparent','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\ORM\\Test\\ts_testparent','objects','id'],            // Werden Objektarrays gelesen
            [5,'Sunhill\\ORM\\Test\\ts_testparent','attributevalues','object_id'],    // Werden Attribute gelesen
            
            
            [6,'Sunhill\\ORM\\Test\\ts_testchild','testchildren','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Test\\ts_testchild','testparents','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Test\\ts_testchild','objects','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Test\\ts_testchild','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\ORM\\Test\\ts_testchild','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\ORM\\Test\\ts_testchild','objects','id'],            // Werden Objektarrays gelesen
            [6,'Sunhill\\ORM\\Test\\ts_testchild','attributevalues','object_id'],    // Werden Attribute gelesen
                        
            [7,'Sunhill\\ORM\\Test\\ts_passthru','passthrus','id']                         // Werden Objekte ohne Simple-Fields geladen
        ];        
    }
}
