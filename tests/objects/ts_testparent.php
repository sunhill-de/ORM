<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_testparent extends \Sunhill\Objects\oo_object {
	
    public static $flag = '';
    
    public $trigger_exception = false;
    
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
		$this->object('parentobject')->set_model('testparent')->set_allowed_objects(['\Sunhill\test\ts_dummy'])->set_default(null);
		$this->arrayofstrings('parentsarray')->set_model('testparent');
		$this->arrayofobjects('parentoarray')->set_model('testparent')->set_allowed_objects(['\Sunhill\Test\ts_dummy']);
	}
	
	public function parentint_changing($from,$to) {
	   self::$flag .= "BINT(".$from."=>$to)";    
	}
	
	public function parentint_changed($from,$to) {
	    self::$flag .= "AINT($from=>$to)";	    
	}
	
	public function parentchar_changing($from,$to) {
	    self::$flag .= "BCHAR($from=>$to)";
	    $this->parentint++;
	}
	
	public function parentchar_changed($from,$to) {
	    self::$flag .= "ACHAR($from=>$to)";
	}

	public function parentfloat_changing($from,$to) {
	    self::$flag .= "BFLOAT($from=>$to)";
	    $this->parentint--;
	}
	
	public function parentfloat_changed($from,$to) {
	    self::$flag .= "AFLOAT($from=>$to)";
	    if ($this->trigger_exception) {
	       $this->parentint--; // Exception
	    }
	}
	
	public function parentobject_changed($from,$to) {
	    if (is_null($from)) {
	        $fromstr = 'NULL';
	    } else {
	        $fromstr = $from->dummyint;
	    }
	    if (is_null($to)) {
	        $tostr = 'NULL';
	    } else {
	        $tostr = $to->dummyint;
	    }
	    self::$flag .= "AOBJECT($fromstr=>$tostr)";
	}
	
	public function child_parentobject_updated($changed_fields) {
	    if (is_array($changed_fields)) {
    	    foreach ($changed_fields as $field => list($from,$to)) {
    	        self::$flag .= "AOBJECT($field:$from=>$to)";
    	    }
	    }
	}
	
	public function parentsarray_changed($new,$deleted) {
	    self::$flag .= "SARRAY(";
        if (!empty($new)) {
    	    self::$flag .= "NEW:";
            foreach ($new as $entry) {
                self::$flag .= "$entry";
    	    }
    	    self::$flag .= ")";
	    }
	    if (!empty($deleted)) {
    	    self::$flag .= "REMOVED:";
    	    foreach ($deleted as $entry) {
    	        self::$flag .= "$entry";
    	    }
    	    self::$flag .= ")";
	    }
	}
	
	public function parentoarray_changed($new,$deleted) {
	}
	
}

