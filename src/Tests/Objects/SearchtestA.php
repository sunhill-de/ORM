<?php
/**
 * @file SearchtestA.php
 * Provides the searchtestA class
 * Lang en
 * Reviewstatus: 2022-07-10
 * Localization: not needed
 * Documentation: not needed
 * Tests: not needed
 * Coverage: not needed
 */
namespace Sunhill\ORM\Tests\Objects;

use Sunhill\ORM\Objects\ORMObject;

/**
 * Only for testing purposes
 * @author klaus
 */
class SearchtestA extends ORMObject {
    
    public static $table_name = 'searchtestA';
    
    public static $object_infos = [
        'name'=>'searchtestA',            // A repetition of static:$object_name @todo see above
        'table'=>'searchtestA',         // A repitition of static:$table_name
        'name_s'=>'Searchtest A object',   // A human readable name in singular
        'name_p'=>'Searchtest A objects',  // A human readable name in plural
        'description'=>'For search tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    
    protected static function setupProperties() {
        parent::setupProperties();
        self::integer('Aint')->searchable();
        self::integer('Anosearch');
        self::varchar('Achar')->searchable();
        self::calculated('Acalc')->searchable();
        self::object('Aobject')->setAllowedObjects(["\\Sunhill\\ORM\\Test\\Dummy"])->searchable();
        self::arrayOfObjects('Aoarray')->setAllowedObjects(["\\Sunhill\\ORM\\Test\\Dummy"])->searchable();
        self::arrayofstrings('Asarray')->searchable();
    }
    
    public function calculate_Acalc() {
        return $this->Aint."=".$this->Achar;
    }
    
    public function unify() {
        $id = searchtestA::search()->where('Acalc','=','ABC')->first();
        
    }
    
    protected static function DefineKeyfields(string $keyfield) {
        list($int,$char) = explode(' ',$keyfield);
        return ['Aint'=>$int,'Achar'=>$char];
    }
}

