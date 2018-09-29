<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_dummy extends \Sunhill\Objects\oo_object {
	
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('dummyint')->set_model('dummy');
	}
	
}

