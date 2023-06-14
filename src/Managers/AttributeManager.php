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

use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Query\BasicQuery;

/**
 * The AttributeManager is accessed via the Attributes facade. It's a singelton class
 */
class AttributeManager 
{

    public function getAvaiableAttributesForClass(string $class, array $without = [])
    {
        $attributes = $this->query()->where('allowed_classes','matches',$class);
        if (!empty($without)) {
            foreach ($without as $entry) {
                $attributes->whereNot('name', $entry);                
            }
        }
        return $attributes->get();
    }
        
    public function query(): BasicQuery
    {
        return Storage::AttributeQuery();
    }
}
 
