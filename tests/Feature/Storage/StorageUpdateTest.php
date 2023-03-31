<?php

namespace Sunhill\ORM\Tests\Feature\Storage;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\StorageMySQL;
class StorageUpdateTest extends StorageBase
{

    /**
     * @group update
     * @dataProvider UpdateProvider
     */
    public function testUpdate($id,$class,$change_callback,$fieldname,$expected) {
        $this->prepare_read();
        $object = new $class();
        $changer = new StorageMySQL($object);
        $change_callback($changer);
        $changer->updateObject($id);

        $readobject = new $class();
        $loader = new StorageMySQL($readobject);
        $loader->loadObject($id);
        $this->assertEquals($expected,$this->getField($loader,$fieldname));
    }
    
    public function UpdateProvider() {
        return [
            [1,Dummy::class,function($storage) {
                $storage->dummyint = ['FROM'=>123,'TO'=>321];
            },'dummyint',321],                       // Wird ein einfaches Feld geändert?
            [9,TestParent::class,function($storage) {
                $storage->parentint = ['FROM'=>111,'TO'=>321];
            },'parentint',321],                       // Wird ein einfaches Feld geändert?
            [9,TestParent::class,function($storage) {
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'OOO'];
            },'parentchar','OOO'],                       // Wird ein einfaches Feld geändert?
            [9,TestParent::class,function($storage) {
                $storage->parentint = ['FROM'=>111,'TO'=>'999'];
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'OOO'];
            },'parentchar','OOO'],  
            [17,TestChild::class,function($storage) {
                $storage->parentint  = ['FROM'=>123,'TO'=>999];
                $storage->parentchar = ['FROM'=>'RRR','TO'=>'OOO'];
                $storage->childint   = ['FROM'=>777,'TO'=>888];
                $storage->childchar  = ['FROM'=>'WWW','TO'=>'PPP'];
            },'parentchar','OOO'],
            [17,TestChild::class,function($storage) {
                $storage->parentint  = ['FROM'=>123,'TO'=>999];
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'OOO'];
                $storage->childint   = ['FROM'=>123,'TO'=>888];
                $storage->childchar  = ['FROM'=>'ABC','TO'=>'PPP'];
            },'childchar','PPP'],
            [25,TestSimpleChild::class,function($storage) {
                $storage->parentint = ['FROM'=>123,'TO'=>'999'];
                $storage->parentchar = ['FROM'=>123,'TO'=>'OOO'];
            },'parentchar','OOO'],
            [4,Dummy::class,function($storage) {
                $storage->attributes = ['int_attribute' => [
                    'id'=>1,
                    'attribute_id'=>1,
                    'value'=>['FROM'=>5,'TO'=>999],
                    'textvalue'=>['FROM'=>'','TO'=>''],
                    'value_id'=>1]];
            },'attributes[int_attribute][value]',999],

// Tagtests            
            [1,Dummy::class,function($storage) { // Nur hinzufügen
                $storage->tags = ['FROM'=>[1,2,4],'TO'=>[1,2,4,5],
                                  'ADD'=>[5],'DELETE'=>[]];
            },'tags',[1,2,4,5]],                         
            [1,Dummy::class,function($storage) { // Ein Elemnt löschen
                $storage->tags = ['FROM'=>[1,2,4],'TO'=>[1,4],
                                  'ADD'=>[],'DELETE'=>[2]];
            },'tags',[1,4]],                            
            [1,Dummy::class,function($storage) { // Alle Elemente löschen
                $storage->tags = ['FROM'=>[1,2,4],'TO'=>[],
                                  'ADD'=>[],'DELETE'=>[1,2,4]];
            },'tags',[]],                            
            [1,Dummy::class,function($storage) { // Kombiertes löschen und hinzufügen
                $storage->tags = ['FROM'=>[1,2,4],'TO'=>[1,3,4],
                                  'ADD'=>[3],'DELETE'=>[2]];
            },'tags',[1,3,4]],                           

// Objektarraytests
            [17,TestChild::class,function($storage) { // Objekt hinzufügen
                $storage->parentoarray = ['FROM'=>[0=>4,1=>5],'TO'=>[0=>4,1=>5,2=>6],
                                          'ADD'=>[2=>6],'DELETE'=>[]];
            },'parentoarray',[4,5,6]],
            [17,TestChild::class,function($storage) { // Objekt löschen
                $storage->parentoarray = ['FROM'=>[0=>4,1=>5],'TO'=>[0=>4],
                                          'ADD'=>[],'DELETE'=>[1=>5]];
            },'parentoarray',[4]],
            [17,TestChild::class,function($storage) { // Alle Objekte löschen
                $storage->parentoarray = ['FROM'=>[0=>4,1=>5],'TO'=>[],
                                          'ADD'=>[],'DELETE'=>[0=>4,1=>5]];
            },'parentoarray',null],
            [17,TestChild::class,function($storage) { // Kombiniertes hinzufügen und löschen
                $storage->parentoarray = ['FROM'=>[0=>4,1=>5],'TO'=>[0=>4,1=>3],
                                          'ADD'=>[1=>3],'DELETE'=>[1=>5]];
            },'parentoarray',[4,3]],
            [17,TestChild::class,function($storage) { // Änderung in anderen Feldern
                $storage->parentchar = ['FROM'=>'ABC','TO'=>'ABCDEF'];
            },'parentoarray',[4,5]],

// Objektfeldtests            
            [17,TestChild::class,function($storage) {
                $storage->parentobject = ['FROM'=>3,'TO'=>12];
            },'parentobject',12],
            [17,TestChild::class,function($storage) {
                $storage->childobject = ['FROM'=>2,'TO'=>12];
            },'childobject',12],
            [17,TestChild::class,function($storage) {
                $storage->parentobject = ['FROM'=>3,'TO'=>null];
            },'parentobject',null],
            
// Stringarraytests            
            [17,TestChild::class,function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'ABCDEFG',1=>'HIJKLMN'],'TO'=>[0=>'ABCDEFG',1=>'HIJKLMN',2=>'OPQRST'],
                                          'ADD'=>[2=>'OPQRST'],'DELETE'=>[]];
            },'parentsarray',['ABCDEFG','HIJKLMN','OPQRST']],
            [17,TestChild::class,function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'ABCDEFG',1=>'HIJKLMN'],'TO'=>[0=>'ABCDEFG'],
                                          'ADD'=>[],'DELETE'=>[1=>'HIJKLMN']];
            },'parentsarray',['ABCDEFG']],
            [17,TestChild::class,function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'ABCDEFG',1=>'HIJKLMN'],'TO'=>[],
                    'ADD'=>[],'DELETE'=>[0=>'ABCDEFG',1=>'HIJKLMN']];
            },'parentsarray',null],
            [17,TestChild::class,function($storage) {
                $storage->parentsarray = ['FROM'=>[0=>'ABCDEFG',1=>'HIJKLMN'],'TO'=>[0=>'ABCDEFG',1=>'OPQRST'],
                                          'ADD'=>[1=>'OPQRST'],'DELETE'=>[1=>'HIJKLMN']];
            },'parentsarray',['ABCDEFG','OPQRST']],
         
// Externalhooks
            ];
    }
    
}
