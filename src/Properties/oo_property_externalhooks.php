<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_externalhooks extends oo_property_field {
	
	protected $features = ['hooks','complex'];
	
	protected $initialized = true;
	protected $defaults_null = true;
	
    /**
     * Läd Externe Hooks aus dem Storage
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\oo_property::do_load()
     */
	protected function do_load(\Sunhill\ORM\Storage\storage_base $loader,$name) {
        $hooks = $loader->get_entity('externalhooks');
        if (empty($hooks)) {
            return;
        }
	    foreach ($hooks as $hook) {
	        $this->owner->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target_id']);
	    }
	    $this->shadow = $this->owner->get_external_hooks();
	}
	
	private function get_target_id($destination) {
	    if (is_int($destination)) {
	        return $destination;
	    } else {
	        return $destination->get_id();
	    }	    
	}
	
	protected function do_insert(\Sunhill\ORM\Storage\storage_base $storage,$name) {	    
	    foreach ($this->owner->get_external_hooks() as $hook) {
	        if (is_int($hook['destination'])) {
	            $target_id = $hook['destination'];
	        } else {    
	            $target_id = $hook['destination']->get_id();
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
	    $this->shadow = $this->owner->get_external_hooks();
	}
	
	/**
	 * @todo bisher kein Coverage, da wir noch keine Hooks hinzugefügt oder gelöscht haben. Tests schreiben
	 * @param array $hook
	 * @param array $array
	 * @return boolean
	 */
	private function hook_in_array(array $hook,array $array) {
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
	
	private function hook_in_a_not_b($a,$b) {
	    $result = [];
	    if (empty($a)) {
	        return [];
	    }
	    if (empty($b)) {
	        return $a;
	    }
	    foreach ($a as $hook) {
	        if (!$this->hook_in_array($hook,$b)) {
	            $result[] = $hook;
	        }
	    }
	    return $result;
	}
	
	private function fill_target_id($target) {
	    foreach($target as $entry) {
	        $entry['target_id'] = $this->get_target_id($entry['destination']);
	    }
	    return $target;
	}
	
	protected function do_update(\Sunhill\ORM\Storage\storage_base $storage,$name) {
	    $add = $this->hook_in_a_not_b($this->owner->get_external_hooks(),$this->shadow);
	    $delete = $this->hook_in_a_not_b($this->shadow,$this->owner->get_external_hooks());
	    $add = $this->fill_target_id($add);
	    $delete = $this->fill_target_id($delete);
	    $external_hooks = [
	        'FROM'=>$this->shadow,
	        'TO'=>$this->owner->get_external_hooks(),
	        'ADD'=>$add,
	        'DELETE'=>$delete,
	        'NEW'=>$add,
	        'REMOVED'=>$delete
	    ];
	    $storage->set_entity('externalhooks', $external_hooks);
	    $this->shadow = $this->owner->get_external_hooks();
	}
}