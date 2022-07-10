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
class SearchtestB extends searchtestA {
    
    public static $table_name = 'searchtestB';
    
    public static $object_infos = [
        'name'=>'searchtestB',            // A repetition of static:$object_name @todo see above
        'table'=>'searchtestB',         // A repitition of static:$table_name
        'name_s'=>'Searchtest B object',   // A human readable name in singular
        'name_p'=>'Searchtest B objects',  // A human readable name in plural
        'description'=>'For search tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    protected static function setupProperties() {
        parent::setupProperties();
        self::integer('Bint')->searchable();
        self::varchar('Bchar')->searchable();
        self::calculated('Bcalc')->searchable();
        self::object('Bobject')->setAllowedObjects(["\\Sunhill\\ORM\\Test\\Dummy"])->searchable();
        self::arrayofstrings('Bsarray')->searchable();
        self::arrayOfObjects('Boarray')->setAllowedObjects(["\\Sunhill\\ORM\\Test\\Dummy"])->searchable();
    }
    
    public function calculate_Bcalc() {
        return $this->Bint."=".$this->Bchar;
    }
    
    protected static function DefineKeyfields(string $keyfield) {
        list($int,$char) = explode(' ',$keyfield);
        return ['Bint'=>$int,'Bchar'=>$char];
    }
    
    
}

