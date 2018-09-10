<?php

class ts_secondlevelchild extends ts_passthru {
	
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('childint');
	}
	
}

