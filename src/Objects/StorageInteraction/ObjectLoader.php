<?php
/**
 * @file ObjectLoader.php
 * Defines the class CollectionLoader. This class is used to load an object from a 
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

use Sunhill\ORM\Facades\Tags;

class ObjectLoader extends CollectionLoader
{
   
    public function handlePropertyTags($property)
    {
        if (!$this->storage->hasEntity('tags')) {
            return;
        }
        $tags = $this->storage->getEntity('tags')->getValue();
        if (empty($tags)) {
            return;
        }
        foreach ($tags as $tag) {
            $tag = Tags::loadTag($tag);
            $this->collection->tags->stick($tag);
        }
    }
    
    protected function additionalRun()
    {
        if (!$this->storage->hasEntity('attributes')) {
            return;
        }
        foreach ($this->storage->getEntity('attributes') as $attribute) {
            $attribute_obj = $this->collection->dynamicAddProperty($attribute->name, $attribute->type);
            $attribute_obj->setAttributeID($attribute->attribute_id);
            $attribute_obj->setValue($attribute->value);            
        }
    }
    
}