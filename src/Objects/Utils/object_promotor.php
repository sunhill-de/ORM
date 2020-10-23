<?php

/**
 * @file object_promotor.php
 * Provides the object_promotor class that is a supporting class for the object manager
 * Lang en
 * Reviewstatus: 2020-10-22
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Classes
 */
namespace Sunhill\ORM\Objects\Utils;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Managers\ObjectManagerException;

class object_promotor {
 
    private $original_name = '';
    private $original_namespace = '';
    
    /**
     * Hebt das momentane Objekt auf eine abgeleitete Klasse an
     * @param String $newclass
     * @throws ObjectException
     * @return oo_object
     */
    public function promote(oo_object $object,string $newclass) {
        $this->original_name = Classes::get_class_name($object);
        if (empty($this->original_name)) {
            throw new ObjectManagerException("The class '$newclass' doesn't exist.");            
        }
        $this->original_namespace = Classes::get_namespace_of_class($this->original_name);
        if (!Classes::is_subclass_of($newclass, $this->original_name)) {
            throw new ObjectManagerException("'$newclass' is not a subclass of '".$this->original_name."'");
        }
        $object->pre_promotion($newclass);
        $newobject = $this->promotion($object,$newclass);
        $newobject->clean_properties();
        $newobject->post_promotion($object);
        return $newobject;
    }
    
    /**
     * Die eigentliche Promovierung
     * @todo Has an direct database access
     * @param String $newclass
     */
    private function promotion(oo_object $oldobject,string $newclass) {
        $new_namespace = Classes::get_namespace_of_class($newclass);
        $newobject = new $new_namespace; // Create a new object
        $newobject->set_id($oldobject->get_id());
        $oldobject->copy_to($newobject);       
        DB::table('objects')->where('id','=',$oldobject->get_id())->update(['classname'=>Classes::get_class_name($newclass)]);
        $newobject->recalculate();
        return $newobject;
    }
    
}
