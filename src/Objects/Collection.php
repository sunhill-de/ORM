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

use Sunhill\ORM\Objects\StorageInteraction\StorageInteractionBase;
use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\StorageBase;

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
use Sunhill\ORM\Objects\StorageInteraction\CollectionLoader;
use Sunhill\ORM\Objects\StorageInteraction\CollectionStorer;
use Sunhill\ORM\Objects\StorageInteraction\CollectionUpdater;

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
    protected static $storageClass = 'collection';
    
	protected static function getClassList()
	{
	    return [static::class];
	}
	
	protected function getLoaderInteraction(): StorageInteractionBase
	{
	    return new CollectionLoader();
	}
	
	protected function getStorerInteraction(): StorageInteractionBase
	{
	    return new CollectionStorer(); 
	}
	
	protected function getUpdaterInteraction(): StorageInteractionBase
	{
        return new CollectionUpdater();	    
	}
	
}
