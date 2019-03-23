<?php

namespace Sunhill\Properties;

class oo_property_tags extends oo_property_arraybase {
	
	protected $type = 'tags';
	
	protected $features = ['tags','array'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function load_tags(int $id) {
	    
	}
	
	public function add(\Sunhill\Objects\oo_tag $tag) {
	    foreach ($this->value as $listed) {
	        if ($listed->get_fullpath() === $tag->get_fullpath()) {
	            return $this; // Gibt es schon
	        }
	    }
	    $this->value[] = $tag;
	    $this->set_dirty(true);
	}
	
	public function load(int $id) {
	    $assigns = \App\tagobjectassign::where('container_id','=',$id)->get();
	    foreach ($assigns as $assign) {
	        $tag = new \Sunhill\Objects\oo_tag($assign->tag_id);
	        $this->add_tag($tag);
	    }	    
	}
	
}