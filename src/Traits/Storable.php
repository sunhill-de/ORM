<?php

/**
 * @file Storable.php
 * Implements the trait Storable that can be include by a property to make it storable into
 * a storage.
 */
namespace Sunhill\ORM\Traits;

trait Storable 
{
    protected $storageID;
    
    protected $storageClass = '';
    
    public function setStorageClass(string $class)
    {
        $this->storageClass = $class;    
    }
    
    public function getID()
    {
        return $this->storageID;
    }
    
    public function setID($id)
    {
        $this->storageID = $id;
    }
    
    public function isStorable(): bool
    {
        return !empty($this->storageClass);
    }
    
    /**
     * A storage class indicates the storage facade, what kind of storage it has to
     * create
     * An empty value means, that this property cannot store itself into the storage
     *
     * @return string
     */
    public function getStorageClass(): string
    {
        return $this->storageClass;
    }
    
    /**
     * A storage name tells the storage facade, what storage it should use
     * @return string
     */
    public function getStorageName(): string
    {
        return '';
    }
    
    /**
     * The storage id tells the storage the unique identification (like an int ID or a
     * timestamp)
     
     * @return NULL
     */
    public function getStorageID()
    {
        return $this->storageID;
    }
    
    
}