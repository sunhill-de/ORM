<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_dummy extends \Sunhill\Objects\oo_object {
	
    public $changestr = '';
    
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('dummyint')->set_model('dummy');
	}
	
	public function tag_added($tag) {
	    $this->changestr .= 'ADD:'.$tag->get_fullpath();
	}
	
	public function tag_deleted($tag) {
	    $this->changestr .= 'DELETE:'.$tag->get_fullpath();	    
	}
}

