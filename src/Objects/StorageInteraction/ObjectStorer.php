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

class ObjectStorer extends CollectionStorer
{
   
    public function handlePropertyTags($property)
    {
        $values = [];
        foreach ($this->tags as $tag) {
            $values[] = $tag->getID();
        }
        $this->storage->createEntity($property->getName(), $this->collection->getInfo('table'))
        ->setValue($value);
    }
    
    public function handlePropertyInformation($property)
    {
        $this->storage->createEntity($property->getName(), $this->collection->getInfo('table'))
        ->setValue($property->getPath());
    }
    
    protected function additionalRun()
    {
    }
    
}