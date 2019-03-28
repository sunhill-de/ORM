<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_dummy extends \Sunhill\Objects\oo_object {
	
    public $changestr = '';
    
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('dummyint')->set_model('dummy');
	}
	
	protected function setup_hooks() {
	    parent::setup_hooks();
	    $this->add_hook('UPDATED_PROPERTY','tag_changed','tags');
	}
	public function tag_changed($change) {
	    if (!empty($change['NEW'])) {
	        $this->changestr .= 'ADD:';
	        foreach ($change['NEW'] as $tag) {
    	       $this->changestr .= $tag->get_fullpath();       
    	    }
	    }
	    if (!empty($change['REMOVED'])) {
	        $this->changestr .= 'REMOVED:';
	        foreach ($change['REMOVED'] as $tag) {
	            $this->changestr .= $tag->get_fullpath();
	        }
	    }
	}
	
}

