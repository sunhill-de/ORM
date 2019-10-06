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
	
}