<?php
/**
 * @file storage_base.php
 * The basic class for storages (at the moment there is only storage_mysql)
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 */

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\ORMObject;

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
abstract class storage_base  {
    
    /** 
     * Speichert das aufrufende Objekt
     * @var ORMObject
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
    public function __construct(ORMObject $caller) {
        $this->caller = $caller;    
    }
    
    abstract public function execute_need_id_queries();
    
    /**
     * @retval array Die Vererbunghirarchie der übergebenen Klasse
     */
    public function get_inheritance() {
        return $this->caller->get_inheritance(true);
    }
    
    /**
     * Liefert das aufrufende Objekt zurück
     * @return ORMObject
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
    
    protected function execute_chain(string $chainname,int $id,$payload=null) {
        $method_name = 'prepare_'.$chainname;
        $module_list = [];
        foreach ($this->modules as $module_name) {
            $full_name = "\\Sunhill\\ORM\\Storage\\storagemodule_".$module_name;
            $module = new $full_name($this);
            if (isset($payload)) {
                $module->$method_name($id,$payload);
            } else {
                $module->$method_name($id);                
            }
            $module_list[] = $module;
        }
        foreach ($module_list as $module) {
            if (isset($payload)) {
                $id = $module->$chainname($id,$payload);
            } else {
                $id = $module->$chainname($id);
            }
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
    
    public function update_object(int $id) {
        return $this->execute_chain('update',$id);    
    }
    
    public function delete_object(int $id) {
        return $this->execute_chain('delete',$id);    
    }
    
    public function degradeObject(int $id,array $degration_info) {
        return $this->execute_chain('degrade',$id,$degration_info);
    }
    
    public function filter_storage($features,$grouping=null) {
        $result = [];
        foreach ($this->entities as $entity => $value) {
            $property = $this->get_caller()->getProperty($entity,true);
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
    
    public function add_needid_query(string $table,array $fixed,string $id_field) {
        $this->caller->add_need_id_query($table, $fixed, $id_field);
    }
}
