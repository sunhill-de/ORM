<?php

namespace Sunhill\ORM\Tests\Feature\Storage;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
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
            [1,Dummy::class,'dummies','id'],                     // Wird ein einfaches Feld gelesen?
            [1,Dummy::class,'objects','id'],                     // Wird ein einfaches Feld gelesen?
            [2,Dummy::class,'dummies','id'],                     // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,Dummy::class,'tagobjectassigns','container_id'],  // Werden die Tags vernünftig ausgelesen?
            [1,Dummy::class,'attributevalues','object_id'],      // Werden Attribute richtig ausgelesen
            
            [9,TestParent::class,'testparents','id'],              // Werden Varcharfelder gelesen
            [9,TestParent::class,'caching','object_id'],             // Werden calculierte Felder gelesen
            [9,TestParent::class,'objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [9,TestParent::class,'stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [9,TestParent::class,'objects','id'],            // Werden Objektarrays gelesen
            [9,TestParent::class,'attributevalues','object_id'],    // Werden Attribute gelesen
            
            
            [17,TestChild::class,'testchildren','id'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'testparents','id'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'objects','id'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [17,TestChild::class,'stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [17,TestChild::class,'objects','id'],            // Werden Objektarrays gelesen
            [17,TestChild::class,'attributevalues','object_id'],    // Werden Attribute gelesen
                        
            [25,TestSimpleChild::class,'testsimplechildren','id']                         // Werden Objekte ohne Simple-Fields geladen
        ];        
    }
}
