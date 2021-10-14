<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use \Sunhill\ORM\Storage\StorageBase;

class PropertyExternalHooks extends PropertyField {
	
	protected $features = ['hooks','complex'];
	
	protected $initialized = true;
	protected $defaults_null = true;
	
    /**
     * Läd Externe Hooks aus dem Storage
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\Property::doLoad()
     */
	protected function doLoad(StorageBase $loader, $name) 
	{
        $hooks = $loader->getEntity('externalhooks');
        if (empty($hooks)) {
            return;
        }
	    foreach ($hooks as $hook) {
	        $this->owner->addHook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target_id']);
	    }
	    $this->shadow = $this->owner->getExternalHooks();
	}
	
	private function get_targetID($destination) 
	{
	    if (is_int($destination)) {
	        return $destination;
	    } else {
	        return $destination->getID();
	    }	    
	}
	
	protected function doInsert(StorageBase $storage, $name) 
	{	    
	    foreach ($this->owner->getExternalHooks() as $hook) {
	        if (is_int($hook['destination'])) {
	            $target_id = $hook['destination'];
	        } else {    
	            $target_id = $hook['destination']->getID();
	        }
	        $line = [
	            'action'=>$hook['action'],
	            'subaction'=>$hook['subaction'],
	            'hook'=>$hook['hook'],
	            'payload'=>$hook['payload'],
	            'target_id'=>$target_id,
	            'destination'=>$hook['destination']
	        ];
	        $storage->entities['externalhooks'][] = $line;
	    }
	    $this->shadow = $this->owner->getExternalHooks();
	}
	
	/**
	 * @todo bisher kein Coverage, da wir noch keine Hooks hinzugefügt oder gelöscht haben. Tests schreiben
	 * @param array $hook
	 * @param array $array
	 * @return boolean
	 */
	private function hookInArray(array $hook, array $array) 
	{
	    foreach ($array as $entry) {
	        if (($entry['action'] == $hook['action']) &&
	            ($entry['subaction'] == $hook['subaction']) &&
	            ($entry['hook'] == $hook['hook']) &&
	            ($entry['target_id'] == $hook['target_id'])) {
	                   return true;
	        }
	            
	    }
	    return false;
	}
	
	private function hookInANotB($a, $b) 
	{
	    $result = [];
	    if (empty($a)) {
	        return [];
	    }
	    if (empty($b)) {
	        return $a;
	    }
	    foreach ($a as $hook) {
	        if (!$this->hookInArray($hook,$b)) {
	            $result[] = $hook;
	        }
	    }
	    return $result;
	}
	
	private function fillTargetID($target) 
	{
	    foreach($target as $entry) {
	        $entry['target_id'] = $this->get_targetID($entry['destination']);
	    }
	    return $target;
	}
	
	protected function doUpdate(StorageBase $storage, $name) 
	{
	    $add = $this->hookInANotB($this->owner->getExternalHooks(),$this->shadow);
	    $delete = $this->hookInANotB($this->shadow,$this->owner->getExternalHooks());
	    $add = $this->fillTargetID($add);
	    $delete = $this->fillTargetID($delete);
	    $external_hooks = [
	        'FROM'=>$this->shadow,
	        'TO'=>$this->owner->getExternalHooks(),
	        'ADD'=>$add,
	        'DELETE'=>$delete,
	        'NEW'=>$add,
	        'REMOVED'=>$delete
	    ];
	    $storage->setEntity('externalhooks', $external_hooks);
	    $this->shadow = $this->owner->getExternalHooks();
	}
}