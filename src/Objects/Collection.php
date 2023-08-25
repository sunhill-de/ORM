<?php
/**
 * @file Collection.php
 * Defines the class Collection. As an extension to PropertyCollection this class just maps a 
 * class with properties to a single table. There is no hirachy as with ORMObject. 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-08-25
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: 0%
 * PSR-State: some type hints missing
 * Tests: PropertyCollection_infoTest
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Storage\StorageBase;

/**
 * Basic class for all classes that have properties.
 *  
 * @author lokal
 */
class Collection extends PropertiesCollection 
{
    use HandlesStorage;
    
    public function getIDName(): string
    {
        return 'id';
    }
    
    public function getIDType(): string
    {
        return 'int';
    }
        
}
