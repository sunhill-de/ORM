<?php namespace Sunhill\ORM\Storage;

/**
 * @file StorageModuleBase.php
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

abstract class StorageModuleBase 
{
    
    protected $storage;
    
    public function __construct(StorageBase $storage) 
    {
        $this->storage = $storage;
    }
    
    /**
     * Bereitet das Laden des Objektes für dieses Modul vor
     * @param int $id
     */
    public function prepare_load(int $id) 
    {
    }
    /**
     * Läd die Daten zum Objekt mit der ID $id aus dem Storage und schreibt diese in das Storage-Objekt
     * @param int $id
     */
    abstract public function load(int $id);
    
    /**
     * Bereitet das Speichern des Objektes für dieses Modul vor
     */
    public function prepare_insert(int $id) 
    {
        
    }
    
    /**
     * Speichert ein neues Objekt im Storage unter der $id oder 0 wenn es sich um das erste Objekt handelt
     * @param int $id
     */
    abstract public function insert(int $id);
    
    /**
     * Bereitet ein Update für dieses Objekt vor
     * @param int $id
     */
    public function prepare_update(int $id) {
        
    }
    
    /**
     * Führt ein Update für das Objekt im Storage aus
     * @param int $id
     */
    abstract public function update(int $id);    
    
    /**
     * Bereitet das Löschen des Objektes vor
     * @param int $id
     */
    public function prepare_delete(int $id) {
        
    }
    
    /**
     * Löscht die Referenzen auf das Objekt mit der übergebenen $id
     * @param int $id
     */
    abstract public function delete(int $id);
    
    public function prepare_degrade(int $id,array $degration_info) {
        
    }
    
    public function degrade(int $id,array $degration_info) {
        // Normalerweise muss ein Property hier nix machen
        return $id;
    }
    
}
