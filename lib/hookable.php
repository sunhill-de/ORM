<?php

namespace Sunhill;

class hookable extends base {

	
	protected $hooks = array();
	protected $external_hooks = array();
	
	public function __construct() {
		//parent::__construct();
		$this->setup_hooks();
	}
		
	/**
	 * Wird aufgerufen, um Hooks für dieses Objekt zu setzen
	 */
	protected function setup_hooks() {
	    // Macht in der Ursprungsvariante nichts
	}

	/**
	 * Fügt einen neuen Hook hinzu
	 * @param string $action
	 * @param string $hook
	 * @param string $subaction
	 * @param hookable $destination
	 */
	public function add_hook(string $action,string $hook,string $subaction='default',$destination=null,$payload=null) {
	    if (is_null($destination)) { $destination = $this; }
	    if ($this->hook_already_installed($action,$hook,$subaction,$destination,$payload)) {
	        return;
	    }
	    if ($destination !== $this) {
	        $this->external_hooks[] = array('action'=>$action,'subaction'=>$subaction,'destination'=>$destination,'payload'=>$payload);
	    }
	    if (!isset($this->hooks[$action])) {
	        $this->hooks[$action] = array();
	    }
	    if (!isset($this->hooks[$action][$subaction])) {
	        $this->hooks[$action][$subaction] = array();
	    }
	    if (strpos($subaction,'.')) {
	        // Es handelt sich um einen komplexen hook
	        $this->set_complex_hook($action,$hook,$subaction,$destination);
	    } else {	    
	       $this->hooks[$action][$subaction][] = array('destination'=>$destination,'hook'=>$hook,'payload'=>$payload);
	    }
	}
	
	private function hook_already_installed($action,$hook,$subaction,$destination,$payload) {
	    if (isset($this->hooks[$action]) && isset($this->hooks[$action][$subaction])) {
	        foreach ($this->hooks[$action][$subaction] as $descriptor) {
	            if (($hook == $descriptor['hook']) && ($this->target_equal($destination,$descriptor['destination']))) {
	                return true;
	            }
	        }
	    }
	    return false;
	}
	
	protected function target_equal($test1,$test2) {
	    return ($test1 === $test2);
	}
	/**
	 * Wird für komplexe Aufgabe aufgerufen
	 * @param string $action
	 * @param string $hook
	 * @param string $subaction
	 * @param unknown $destination
	 */
	protected function set_complex_hook(string $action,string $hook,string $subaction,$destination) {
	    $this->hooks[$action][$subaction][] = array('destination'=>$destination,'hook'=>$hook);	    
	}
	
	/**
	 * Prüft, ob es entsprechende Hooks gibt
	 * @param string $action
	 * @param string $subaction
	 * @param array $params
	 */
	protected function check_for_hook(string $action,$subaction='default',array $params=null) {
	    if (isset($this->hooks[$action]) && isset($this->hooks[$action][$subaction])) {
	        foreach ($this->hooks[$action][$subaction] as $descriptor) {
                $destination = $descriptor['destination'];
                $hook = $descriptor['hook'];
	            if (is_int($destination)) {
	                $destination = \Sunhill\Objects\oo_object::load_object_of($descriptor['destination']);
	            }
	            if (!isset($params)) {
	                $params = array();
	            }
	            $params['action']    = $action;
	            $params['subaction'] = $subaction;
	            $params['payload'] = $descriptor['payload'];
	            $destination->$hook($params);
	        }
	    }
	}
	
}