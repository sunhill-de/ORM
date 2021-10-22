<?php
/**
 * @file Hookable.php
 * A basic class for classes that use hooks to trigger certain events
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-08-20
 * Localization: none
 * Documentation: complete
 * Tests: Unit/HookableTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM;

use Sunhill\ORM\Facades\Objects;
use Sunhill\Basic\Loggable;

/**
 * A basic class for classes, that make use of hooks
 * The following hooks are predefined:
 * @defgroup Hooks
 * - CONSTRUCTED is called whenever a new object is created
 * @author Klaus
 */
class Hookable extends Loggable 
{

	
	protected $hooks = array();
	protected $external_hooks = array();
	
	/**
	 * Derrived classes have to call this constructor so that the initialization of the hook
	 * system will be performed. The initialization will take place via a call of ->setupHooks(). 
	 * Further the hook for CONSTRCUTED will be called if it exists
	 */
	public function __construct() 
    {
		parent::__construct();
		$this->setupHooks();
		$this->checkForHook('CONSTRUCTED');		
	}
		
    /**
     * This method will initialize the hooks for this class
     */
	protected function setupHooks() 
    {
	    // Does nothing in the basic class
	}

	/**
	 * Adds a new hook
	 * @param string $action
	 * @param string $hook
	 * @param string $subaction
	 * @param Hookable $destination
	 */
	public function addHook(string $action, string $hook, string $subaction = 'default', $destination = null, $payload=null) 
    {
	    if (is_null($destination)) { 
            $destination = $this; 
        }
	    if ($this->hookAlreadyInstalled($action,$hook,$subaction,$destination,$payload)) {
	        return;
	    }
	    if ($destination !== $this) {
            $this->setExternalHook($action,$subaction,$destination,$payload,$hook);
	    }
	    if (!isset($this->hooks[$action])) {
	        $this->hooks[$action] = array();
	    }
	    if (!isset($this->hooks[$action][$subaction])) {
	        $this->hooks[$action][$subaction] = array();
	    }
	    if (strpos($subaction,'.')) {
	        // It's a complex hook
	        $this->setComplexHook($action,$hook,$subaction,$destination);
	    } else {	    
	       $this->hooks[$action][$subaction][] = array('destination'=>$destination,'hook'=>$hook,'payload'=>$payload);
	    }
	}
	
	protected function setExternalHook(string $action, string $subaction, $destination, $payload, string $hook) 
    {
	    $this->external_hooks[] = array('action'=>$action,'subaction'=>$subaction,'destination'=>$destination,'payload'=>$payload);	    
	}
	
	private function hookAlreadyInstalled($action,$hook,$subaction,$destination,$payload) 
    {
	    if (isset($this->hooks[$action]) && isset($this->hooks[$action][$subaction])) {
	        foreach ($this->hooks[$action][$subaction] as $Descriptor) {
	            if (($hook == $Descriptor['hook']) && ($this->target_equal($destination,$Descriptor['destination']))) {
	                return true;
	            }
	        }
	    }
	    return false;
	}
	
	protected function target_equal($test1,$test2) 
    {
	    return ($test1 === $test2);
	}
	
    /**
	 * Is called for complex hooks
	 * @param string $action
	 * @param string $hook
	 * @param string $subaction
	 * @param unknown $destination
	 */
	protected function setComplexHook(string $action,string $hook,string $subaction,$destination) 
    {
	    $this->hooks[$action][$subaction][] = array('destination'=>$destination,'hook'=>$hook);	    
	}
	
	/**
	 * Checks if there are hooks for this event
	 * @param string $action
	 * @param string $subaction
	 * @param array $params
	 */
	public function checkForHook(string $action, $subaction = 'default', array $params=null) 
    {
	    if (isset($this->hooks[$action]) && isset($this->hooks[$action][$subaction])) {
	        foreach ($this->hooks[$action][$subaction] as $Descriptor) {
                $destination = $Descriptor['destination'];
                $hook = $Descriptor['hook'];
	            if (is_int($destination)) {
	                $destination = Objects::load($Descriptor['destination']);
	            }
	            if (!isset($params)) {
	                $params = array();
	            }
	            $params['action']    = $action;
	            $params['subaction'] = $subaction;
	            $params['payload'] = $Descriptor['payload'];
	            $destination->$hook($params);
	        }
	    }
	}

	public function getExternalHooks() 
    {
	    $result = [];
	    foreach ($this->hooks as $actionname=>$actions) {
	        foreach ($actions as $subactionname=>$subactions) {
	            foreach ($subactions as $hook) {
	                if (is_int($hook['destination']) || ($hook['destination'] !== $this)) {
	                    $hook['action'] = $actionname;
	                    $hook['subaction'] = $subactionname;
	                    $hook['target_id'] = is_int($hook['destination'])?$hook['destination']:$hook['destination']->getID();
	                    $result[] = $hook;
	                }
	            }
	        }
	    }
	    return $result;
	}
		
}
