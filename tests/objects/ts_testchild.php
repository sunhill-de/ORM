<?php
namespace Sunhill\Test;

class ts_testchild extends ts_testparent {
    public static $table_name = 'testchildren';
    
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('childint')->set_model('testchild');
		$this->varchar('childchar')->set_model('testchild');
		$this->float('childfloat')->set_model('testchild');
		$this->text('childtext')->set_model('testchild');
		$this->datetime('childdatetime')->set_model('testchild');
		$this->date('childdate')->set_model('testchild');
		$this->time('childtime')->set_model('testchild');
		$this->enum('childenum')->set_model('testchild')->set_values(['testA','testB','testC']);
		$this->object('childobject')->set_model('testchild')->set_allowed_objects(['\Sunhill\test\ts_dummy'])->set_default(null);;
		$this->arrayofstrings('childsarray')->set_model('testchild');
		$this->arrayofobjects('childoarray')->set_model('testchild')->set_allowed_objects(['\Sunhill\test\ts_dummy']);
	}
	
}

