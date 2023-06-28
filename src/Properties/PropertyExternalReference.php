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
use Sunhill\ORM\Properties\Utils\Cachable;

class PropertyExternalReference extends AtomarProperty
{

    use Cachable;
    
	protected static $type = 'string';
		
	protected $initialized = true;

	protected $read_only = true;
	
	protected $table = '';
	
	protected $external_key = '';
	
	protected $internal_key = '';
	
	protected $query_modifier;
	
	protected $list = false;
	
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
		
	public function queryModifier($modifier_callback): PropertyExternalReference
	{
	    $this->query_modifier = $modifier_callback;
	    return $this;
	}
	
	public function setList(bool $list = true): PropertyExternalReference
	{
	   $this->list = $list;  
	   return $this;
	}
	
	protected function retrieveValue()
	{
        $internal_key_name = $this->internal_key;
        $internal_key_value = $this->getActualPropertiesCollection()->$internal_key_name;
        $query = DB::table($this->table)->where($this->external_key,$internal_key_value);
        $modifier = $this->query_modifier;
        if (is_callable($modifier)) {
            $query = $modifier($query);
        }
        if ($this->list) {
            $this->setValue($query->get());            
        } else {
            $this->setValue($query->first());
        }
	}
		
}
