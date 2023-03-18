<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\StorageMySQL;

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
        $changer = new StorageMySQL($object);
        $this->assertDatabaseHas($table,[$field=>$id]);
        $changer->deleteObject($id);        
        $this->assertDatabaseMissing($table,[$field=>$id]);
    }
    
    public function DeleteProvider() {
        return [
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','dummies','id'],                     // Wird ein einfaches Feld gelesen?
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','objects','id'],                     // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\ORM\\Tests\\Objects\\Dummy','dummies','id'],                     // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','tagobjectassigns','container_id'],  // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','attributevalues','object_id'],      // Werden Attribute richtig ausgelesen
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','externalhooks','container_id'],     // Werden Referenzen auf externe Hooks gelöscht (von $id)
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','externalhooks','target_id'],        // Werden Referenzen auf externe Hooks gelöscht (auf $id)
            
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','testparents','id'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','caching','object_id'],             // Werden calculierte Felder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','objects','id'],            // Werden Objektarrays gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','attributevalues','object_id'],    // Werden Attribute gelesen
            
            
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','testchildren','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','testparents','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','objects','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','objects','id'],            // Werden Objektarrays gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','attributevalues','object_id'],    // Werden Attribute gelesen
                        
            [7,'Sunhill\\ORM\\Tests\\Objects\\Passthru','passthrus','id']                         // Werden Objekte ohne Simple-Fields geladen
        ];        
    }
}
