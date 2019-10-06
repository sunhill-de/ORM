<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_externalhooks extends oo_property_field {
	
	protected $features = ['hooks','complex'];
	
	protected $initialized = true;
	protected $defaults_null = true;
	
    /**
     * Läd Externe Hooks aus dem Storage
     * {@inheritDoc}
     * @see \Sunhill\Properties\oo_property::do_load()
     */
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name) {
        $hooks = $loader->get_entity('externalhooks');
        if (empty($hooks)) {
            return;
        }
	    foreach ($hooks as $hook) {
	        $this->owner->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target_id']);
	    }
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	   $this->deleted($id);
	   $this->inserted($id);
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	   $hooks = $this->owner->get_external_hooks();
	   foreach ($hooks as $hook) {
    	   $entry = new \App\externalhook();
           $entry->container_id = $id;
           $entry->target_id = is_int($hook['destination'])?$hook['destination']:$hook['destination']->get_id();
           $entry->action = $hook['action'];
           $entry->subaction = $hook['subaction'];
           $entry->hook = $hook['hook'];
           $entry->payload = '';
           $entry->save();
	   }
	   $this->set_dirty(false);
	}
	
	public function deleted(int $id) {
	    \App\externalhook::where('container_id','=',$id)->delete();
	    $this->set_dirty(false);
	}
	
}