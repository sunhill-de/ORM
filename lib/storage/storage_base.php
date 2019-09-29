<?php

namespace Sunhill\Storage;

/**
 * Alle Exceptions innhalb der Storages und ihrer Module sollten von StorageException abgeleitet werden
 * @author Klaus
 *
 */
class StorageException extends \Exception {}

/**
 * Basisklasse für Storages. Die abgeleiteten Klassen müssen die protected property $modules definieren, welche die eigentlichen
 * Module für die Entity-Klassen läd.
 * Die für das Interface zu den Objekten wichtigen Methoden sind:
 * - load_object($id)
 * - insert_object()
 * - update_object($id)
 * - delete_object($id)
 * 
 * @author lokal
 *
 */
class storage_base  {
    
    /** 
     * Speichert das aufrufende Objekt
     * @var \Sunhill\Objects\oo_object
     */
    protected $caller;
    
    /**
     * Speichert die einzelnen Entities
     * @var array
     * @todo Ist zur Zeit public, was eigentlich nicht so schön ist, vielleicht kann man das eleganter Lösen
     */
    public $entities = [];
    
    /**
     * Konstruktor, übernimmt das aufrufende Objekt als Parameter.
     * @param unknown $caller
     */
    public function __construct(\Sunhill\Objects\oo_object $caller) {
        $this->caller = $caller;    
    }
    
    /**
     * @retval array Die Vererbunghirarchie der übergebenen Klasse
     */
    public function get_inheritance() {
        return $this->caller->get_inheritance(true);
    }
    
    /**
     * Liefert das aufrufende Objekt zurück
     * @return \Sunhill\Objects\oo_object
     */
    public function get_caller() {
        return $this->caller;    
    }
    
    /**
     * Liefert den Entity-Eintrag für $name zurück oder null, wenn dieser nicht defniert ist
     */
    public function get_entity(string $name) {
        if (!isset($this->entities[$name])) {
            return null;
        } else {
            return $this->entities[$name];
        }
    }
    
    /**
     * Wrapper für get_entity
     * @param string $name
     * @return unknown
     */
    public function __get(string $name) {
        return $this->get_entity($name);
    }
    
    /**
     * Schreibt den Entity-Eintrag für $name
     * @param string $name
     * @param unknown $value
     */
    public function set_entity(string $name,$value) {
        $this->entities[$name] = $value;
    }
    
    /**
     * Wrapper für set_entity
     */
    public function __set(string $name,$value) {
        return $this->set_entity($name,$value);
    }    
    
    protected function execute_chain(string $chainname,int $id) {
        $method_name = 'prepare_'.$chainname;
        $module_list = [];
        foreach ($this->modules as $module_name) {
            $full_name = "\\Sunhill\\Storage\\storagemodule_".$module_name;
            $module = new $full_name($this);
            $module->$method_name($id);
            $module_list[] = $module;
        }
        foreach ($module_list as $module) {
            $id = $module->$chainname($id);
        }
        return $id;
    }
    
    /**
     * Läd das Objekt mit der ID $id
     * @param int $id
     */
    public function load_object(int $id) {
        $this->entities = ['id'=> $id,'tags'=>[],'attributes'=>[],'externalhooks'=>[]];
        return $this->execute_chain('load',$id);
    }
    
    public function insert_object(int $id=0) {
        return $this->execute_chain('insert',$id);
    }
    
    
    public function filter_storage($features,$grouping=null) {
        $result = [];
        foreach ($this->entities as $entity => $value) {
            $property = $this->get_caller()->get_property($entity,true);
            if (is_null($property)) { continue; }
            if (is_array($features)) {
                $pass = true;
                foreach ($features as $feature) {
                    if (!$property->has_feature($feature)) {
                        $pass = false;
                    }
                }
            } else {
                $pass = $property->has_feature($features);   
            }
            if ($pass) {
              // Dieses Property hat die Filter überstanden, jetzt noch gruppieren
                if (isset($grouping)) {
                    $group_value = $property->$grouping;
                    if (isset($result[$group_value])) {
                        $result[$group_value][$entity] = $value;
                    } else {
                        $result[$group_value] = [$entity=>$value];
                    }
                } else {
                    $result[$entity] = $value;
                }
            }
        }
        return $result;
    }
    
}
