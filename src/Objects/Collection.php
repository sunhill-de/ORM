<?php
/**
 * @file Collection.php
 * Defines the class Collection. As an extension to PropertyCollection this class just maps a 
 * class with properties to a single table. There is no hirachy as with ORMObject. 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: unknown
 * PSR-State: some type hints missing
 * Tests: PropertyCollection_infoTest
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Search\QueryBuilder;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Facades\Storage;

use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyPropertyCollection;
use Sunhill\ORM\Properties\Commitable;

/**
 * Basic class for all classes that have properties.
 *  
 * @author lokal
 */
class Collection extends PropertyCollection 
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
    
    protected function preCreation(StorageBase $storage)
    {
        
    }
    
    protected function postCreation(StorageBase $storage)
    {
        
    }
    
    protected function preUpdate(StorageBase $storage)
    {
        
    }
    
    protected function postUpdate(StorageBase $storage)
    {
        
    }
    
    /**
     * Indicates the storage class
     * @var string
     */
    protected $storageClass = 'Collection';
    
	public static function search() {
	}
	
	
}
