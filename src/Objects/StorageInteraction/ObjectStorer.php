<?php
/**
 * @file ObjectStorer.php
 * Defines the class CollectionStorer. This class is used to atore an object to a 
 * storage 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-22
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 * PSR-State: complete
 * Tests: none
 */

namespace Sunhill\ORM\Objects\StorageInteraction;

use Sunhill\ORM\Utils\UUIDGenerator;
use Sunhill\ORM\Utils\ObjectDataGenerator;
use Sunhill\ORM\Facades\ObjectData;

class ObjectStorer extends CollectionStorer
{
   
    public function handlePropertyTags($property)
    {
        $values = [];
        foreach ($this->collection->tags as $tag) {
            $values[] = $tag->getID();
        }
        $this->storage->createEntity($property->getName(), 'objects')
        ->setValue($values);
    }
    
    public function handlePropertyInformation($property)
    {
        $this->storage->createEntity($property->getName(), $this->collection->getInfo('table'))
        ->setValue($property->getPath());
    }
    
    protected function preRun()
    {
        $this->collection->_uuid  = ObjectData::getUUID();
        $this->collection->_created_at = ObjectData::getDBTime();
        $this->collection->_updated_at = ObjectData::getDBTime();
    }
    
    protected function additionalRun()
    {
    }
    
}