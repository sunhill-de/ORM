<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class StorageUpdateTest extends StorageBase
{

    /**
     * @group update
     * @dataProvider UpdateProvider
     */
    public function testUpdate($id,$class,$change_callback,$fieldname,$expected) {
        $this->prepare_read();
        $object = new $class();
        $changer = new \Sunhill\ORM\Storage\storageMySQL($object);
        $change_callback($changer);
        $changer->updateObject($id);

        $readobject = new $class();
        $loader = new \Sunhill\ORM\Storage\storageMySQL($readobject);
        $loader->loadObject($id);
        $this->assertEquals($expected,$this->get_field($loader,$fieldname));
    }
    
    public function UpdateProvider() {
        return [
            [1,'Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($storage) {
                $storage->dummyint = ['FROM'=>123,'TO'=>321];
            },'dummyint',321],                       // Wird ein einfaches Feld geändert?
            [5,'Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($storage) {
                $storage->parentint = ['FROM'=>123,'TO'=>321];
            },'parentint',321],                       // Wird ein einfaches Feld geändert?
            [5,'Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($storage) {
                $storage->parentchar = ['FROM'=>'AAA','TO'=>'OOO'];
            },'parentchar','OOO'],                       // Wird ein einfaches Feld geändert?
            [5,'Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($storage) {
                $storage->parentint = ['FROM'=>123,'TO'=>'999'];
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'OOO'];
            },'parentchar','OOO'],  
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentint  = ['FROM'=>123,'TO'=>999];
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'OOO'];
                $storage->childint   = ['FROM'=>123,'TO'=>888];
                $storage->childchar  = ['FROM'=>'ABC','TO'=>'PPP'];
            },'parentchar','OOO'],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentint  = ['FROM'=>123,'TO'=>999];
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'OOO'];
                $storage->childint   = ['FROM'=>123,'TO'=>888];
                $storage->childchar  = ['FROM'=>'ABC','TO'=>'PPP'];
            },'childchar','PPP'],
            [7,'Sunhill\\ORM\\Tests\\Objects\\ts_passthru',function($storage) {
                $storage->parentint = ['FROM'=>123,'TO'=>'999'];
                $storage->parentchar = ['FROM'=>123,'TO'=>'OOO'];
            },'parentchar','OOO'],
            [1,'Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($storage) {
                $storage->attributes = ['int_attribute' => [
                    'id'=>1,
                    'attribute_id'=>1,
                    'value'=>['FROM'=>123,'TO'=>999],
                    'textvalue'=>['FROM'=>'','TO'=>''],
                    'value_id'=>1]];
            },'attributes[int_attribute][value]',999],

// Tagtests            
            [1,'Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($storage) { // Nur hinzufügen
                $storage->tags = ['FROM'=>[1,2],'TO'=>[1,2,3],
                                  'ADD'=>[3],'DELETE'=>[]];
            },'tags',[1,2,3]],                         
            [1,'Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($storage) { // Ein Elemnt löschen
                $storage->tags = ['FROM'=>[1,2],'TO'=>[1],
                                  'ADD'=>[],'DELETE'=>[2]];
            },'tags',[1]],                            
            [1,'Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($storage) { // Alle Elemente löschen
                $storage->tags = ['FROM'=>[1,2],'TO'=>[],
                                  'ADD'=>[],'DELETE'=>[1,2]];
            },'tags',[]],                            
            [1,'Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($storage) { // Kombiertes löschen und hinzufügen
                $storage->tags = ['FROM'=>[1,2],'TO'=>[1,3],
                                  'ADD'=>[3],'DELETE'=>[2]];
            },'tags',[1,3]],                           

// Objektarraytests
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) { // Objekt hinzufügen
                $storage->parentoarray = ['FROM'=>[0=>1,1=>2],'TO'=>[0=>1,1=>2,2=>3],
                                          'ADD'=>[2=>3],'DELETE'=>[]];
            },'parentoarray',[1,2,3]],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) { // Objekt löschen
                $storage->parentoarray = ['FROM'=>[0=>1,1=>2],'TO'=>[0=>1],
                                          'ADD'=>[],'DELETE'=>[1=>2]];
            },'parentoarray',[1]],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) { // Alle Objekte löschen
                $storage->parentoarray = ['FROM'=>[0=>1,1=>2],'TO'=>[],
                                          'ADD'=>[],'DELETE'=>[0=>1,1=>2]];
            },'parentoarray',null],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) { // Kombiniertes hinzufügen und löschen
                $storage->parentoarray = ['FROM'=>[0=>1,1=>2],'TO'=>[0=>1,1=>3],
                                          'ADD'=>[1=>3],'DELETE'=>[1=>2]];
            },'parentoarray',[1,3]],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) { // Änderung in anderen Feldern
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'ABCDEF'];
            },'parentoarray',[1,2]],

// Objektfeldtests            
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentobject = ['FROM'=>3,'TO'=>12];
            },'parentobject',12],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->childobject = ['FROM'=>2,'TO'=>12];
            },'childobject',12],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentobject = ['FROM'=>3,'TO'=>null];
            },'parentobject',null],
            
// Stringarraytests            
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'Parent0',1=>'Parent1'],'TO'=>[0=>'Parent0',1=>'Parent1',2=>'Parent2'],
                                          'ADD'=>[2=>'Parent2'],'DELETE'=>[]];
            },'parentsarray',['Parent0','Parent1','Parent2']],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'Parent0',1=>'Parent1'],'TO'=>[0=>'Parent0'],
                                          'ADD'=>[],'DELETE'=>[1=>'Parent1']];
            },'parentsarray',['Parent0']],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'Parent0',1=>'Parent1'],'TO'=>[],
                                          'ADD'=>[],'DELETE'=>[0=>'Parent0',1=>'Parent1']];
            },'parentsarray',null],
            [6,'Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'Parent0',1=>'Parent1'],'TO'=>[0=>'Parent0',1=>'Parent2'],
                                          'ADD'=>[1=>'Parent2'],'DELETE'=>[1=>'Parent1']];
            },'parentsarray',['Parent0','Parent2']],
         
// Externalhooks
            ];
    }
    
}
