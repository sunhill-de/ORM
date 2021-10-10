<?php

/**
 * @file ObjectPromotor.php
 * Provides the ObjectPromotor class that is a supporting class for the object manager
 * Lang en
 * Reviewstatus: 2021-10-06
 * Localization: complete
 * Documentation: unknown
 * Tests: Feature/Objects/Utils/ObjectPromoteTest
 * Coverage: unknown
 * Dependencies: Classes
 * PSR-State: complete
 */
namespace Sunhill\ORM\Objects\Utils;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\ObjectException;

class ObjectPromotor 
{
 
    private $original_name = '';
    private $original_namespace = '';
    
    /**
     * Raises the given object to a derrived object. 
     * @param $newclass has to be a child of the original class of $object. 
     * @param String $newclass
     * @throws ObjectException 
     * @return ORMObject
     */
    public function promote(ORMObject $object, string $newclass): ORMObject 
    {
        $this->original_name = Classes::getClassName($object);
        if (empty(Classes::getClassName($object))) {
            throw new ObjectException(__("The target class ':newclass' doesn't exist.",['newclass'=>$newclass]));            
        }
        $this->original_namespace = Classes::getNamespaceOfClass($this->original_name);
        if (!Classes::isSubclassOf($newclass, $this->original_namespace)) {
            throw new ObjectManagerException(__("':newclass' is not a subclass of ':oldclass'",['newclass'=>$newclass,'oldclass'=>$this->original_name]));
        }
        $object->prePromotion($newclass);
        $newobject = $this->promotion($object,$newclass);
        $newobject->cleanProperties();
        $newobject->postPromotion($object);
        return $newobject;
    }
    
    /**
     * Does the promotion itself
     * @todo Has an direct database access
     * @param String $newclass
     */
    private function promotion(ORMObject $oldobject, string $newclass): ORMObject
    {
        $new_namespace = Classes::getNamespaceOfClass($newclass);
        $newobject = new $new_namespace; // Create a new object
        $newobject->setID($oldobject->getID()); // Copy the ID
        $oldobject->copyTo($newobject); // Copy the common properties      
        DB::table('objects')->where('id','=',$oldobject->get_id())->update(['classname'=>Classes::getClassName($newclass)]); // Update the class in the database
        $newobject->recalculate(); // Recalculate the calculated fields  
        $newobject->cleanProperties(); // Make everything clean
        return $newobject; // And return 
    }
    
}
