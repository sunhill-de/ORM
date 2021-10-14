<?php
/**
 * @file StorageBase.php
 * The basic class for storages (at the moment there is only StorageMySQL)
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\ORMObject;

/**
 * Basisklasse für Storages. Die abgeleiteten Klassen müssen die protected property $modules definieren, welche die eigentlichen
 * Module für die Entity-Klassen läd.
 * Die für das Interface zu den Objekten wichtigen Methoden sind:
 * - loadObject($id)
 * - insertObject()
 * - updateObject($id)
 * - deleteObject($id)
 * 
 * @author lokal
 *
 */
abstract class StorageBase  
{
    
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
    public function __construct(ORMObject $caller) 
    {
        $this->caller = $caller;    
    }
    
    abstract public function executeNeedIDQueries();
    
    /**
     * @retval array Die Vererbunghirarchie der übergebenen Klasse
     */
    public function getInheritance() 
    {
        return $this->caller->getInheritance(true);
    }
    
    /**
     * Liefert das aufrufende Objekt zurück
     * @return ORMObject
     */
    public function getCaller() 
    {
        return $this->caller;    
    }
    
    /**
     * Liefert den Entity-Eintrag für $name zurück oder null, wenn dieser nicht defniert ist
     */
    public function getEntity(string $name) 
    {
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
    public function __get(string $name) 
    {
        return $this->getEntity($name);
    }
    
    /**
     * Schreibt den Entity-Eintrag für $name
     * @param string $name
     * @param unknown $value
     */
    public function setEntity(string $name, $value) 
    {
        $this->entities[$name] = $value;
    }
    
    /**
     * Wrapper für set_entity
     */
    public function __set(string $name, $value) 
    {
        return $this->setEntity($name,$value);
    }    
    
    protected function executeChain(string $chainname, int $id, $payload = null) 
    {
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
    public function loadObject(int $id) 
    {
        $this->entities = ['id'=> $id,'tags'=>[],'attributes'=>[],'externalhooks'=>[]];
        return $this->executeChain('load',$id);
    }
    
    public function insertObject(int $id = 0) 
    {
        return $this->executeChain('insert',$id);
    }
    
    public function updateObject(int $id) 
    {
        return $this->executeChain('update',$id);    
    }
    
    public function deleteObject(int $id) 
    {
        return $this->executeChain('delete',$id);    
    }
    
    public function degradeObject(int $id, array $degration_info) 
    {
        return $this->executeChain('degrade',$id,$degration_info);
    }
    
    public function filterStorage($features, $grouping = null) 
    {
        $result = [];
        foreach ($this->entities as $entity => $value) {
            $property = $this->getCaller()->getProperty($entity,true);
            if (is_null($property)) { continue; }
            if (is_array($features)) {
                $pass = true;
                foreach ($features as $feature) {
                    if (!$property->hasFeature($feature)) {
                        $pass = false;
                    }
                }
            } else {
                $pass = $property->hasFeature($features);   
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
    
    public function addNeedIDQuery(string $table, array $fixed, string $id_field) 
    {
        $this->caller->addNeedIDQuery($table, $fixed, $id_field);
    }
}
