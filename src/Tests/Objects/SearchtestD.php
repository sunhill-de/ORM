<?php
/**
 * @file SearchtestC.php
 * Provides the searchtestC class
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
class SearchtestD extends ORMObject {
    
    public static $table_name = 'searchtestD';
    
    public static $object_infos = [
        'name'=>'searchtestD',            // A repetition of static:$object_name @todo see above
        'table'=>'searchtestD',         // A repitition of static:$table_name
        'name_s'=>'Searchtest D object',   // A human readable name in singular
        'name_p'=>'Searchtest D objects',  // A human readable name in plural
        'description'=>'For search tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    protected static function setupProperties() {
        parent::setupProperties();
        self::varchar('Dchar')->searchable();
        self::object('Dobject')->setAllowedObjects(["searchtestA","searchtestD"])->searchable();
    }
    
}

