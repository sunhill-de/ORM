<?php
namespace Sunhill\Test;

class ts_referenceonly extends \Sunhill\Objects\oo_object {
	
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('testint')->set_model('referenceonly');
		$this->object('testobject')->set_model('referenceonly')->set_allowed_objects(['\Sunhill\test\ts_dummy','\Sunhill\test\ts_referenceonly'])->set_default(null);;
		$this->arrayofobjects('testoarray')->set_model('referenceonly')->set_allowed_objects(['\Sunhill\test\ts_dummy','\Sunhill\test\ts_referenceonly']);
	}
	
}

