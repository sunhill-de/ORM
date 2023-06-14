<?php
/**
 * @file PropertyObject.php
 * Provides an access to a object field
 * Lang de,en
 * Reviewstatus: 2021-10-14
 * Localization: none
 * Documentation: incomplete
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: incomplete
 * Dependencies: LazyIDLoading
 */

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Storage\StorageBase;

class PropertyExternalReference extends AtomarProperty
{
	
	protected static $type = 'string';
		
	protected $initialized = true;

	protected $read_only = true;
	
	protected $table = '';
	
	protected $external_key = '';
	
	protected $internal_key = '';
	
	public function setExternalTable(string $table): PropertyExternalReference
	{
	    $this->table = $table;
	    return $this;
	}
	
	public function setExternalKey(string $key): PropertyExternalReference
	{
	    $this->external_key = $key;
	    return $this;
	}
	
	public function setInternalKey(string $key): PropertyExternalReference
	{
	    $this->internal_key = $key;
	    return $this;
	}
		
	public function fetchReference()
	{
        $internal_key_name = $this->internal_key;
        $internal_key_value = $this->getOwner()->$internal_key_name;
	    $this->setValue(DB::table($this->table)->where($this->external_key,$internal_key_value)->first());
	}
	
	/**
	 * A calculated field is never uninitialized, if it is marked a so, do recalculate
	 */
	protected function initializeValue(): bool
	{
	    $this->fetchReference();
	    return true;
	}
	
	public function loadFromStorage(StorageBase $storage) 
	{
	   // External references do not store itself to storages
	}
	
	public function insertIntoStorage(StorageBase $storage) 
	{
	    // External references do not store itself to storages
	}

	public function updateToStorage(StorageBase $storage)
	{
	    // External references do not store itself to storages
	}
	
}
