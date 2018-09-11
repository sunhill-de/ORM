<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_testparent extends \Sunhill\Objects\oo_object {
	
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('parentint')->set_model('testparent');
		$this->varchar('parentchar')->set_model('testparent');
		$this->float('parentfloat')->set_model('testparent');
		$this->text('parenttext')->set_model('testparent');
		$this->datetime('parentdatetime')->set_model('testparent');
		$this->date('parentdate')->set_model('testparent');
		$this->time('parenttime')->set_model('testparent');
		$this->enum('parentenum')->set_model('testparent')->set_values(['testA','testB','testC']);
		$this->object('parentobject')->set_model('testparent')->set_allowed_objects(['ts_testparent']);
		$this->arrayofstrings('parentsarray')->set_model('testparent');
		$this->arrayofobjects('parentoarray')->set_model('testparent');
	}
	
}

