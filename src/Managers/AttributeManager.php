<?php
 
/**
 * @file AttributeManager.php
 * Provides the AttributeManager object for accessing information about attributes
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-08
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerTagTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;

/**
 * The AttributeManager is accessed via the Attributes facade. It's a singelton class
 */
class AttributeManager 
{

    /**
     * Returns all attributes from the database. If offset is set the entries start
     * with the $offset-th entry, if limit is set, the result entries are limited to 
     * that value.
     * @param int $offset
     * @param int $limit
     */
    public function getAllAttributes(int $offset = 0, int $limit = 0)
    {
        $query = DB::table('attributes');
        if ($offset) {
            $query = $query->offset($offset);
        }
        if ($limit) {
            $query = $query->limit($limit);
        }
        return $query->get();
    }
 
    public function getCount(): int
    {
        $result = DB::table('attributes')->count();
        return $result;
    }
    
    public function addAttribute(string $name, string $type, string $allowed_objects, string $property)
    {
        DB::table('attributes')->insert(
            [
                'name'=>$name,
                'type'=>$type,
                'allowedobjects'=>$allowed_objects,
                'property'=>$property                
            ]);
    }
}
 
