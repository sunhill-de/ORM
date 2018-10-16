<?php

namespace Sunhill\Test;

use Sunhill\Objects;

class ts_testparent extends \Sunhill\Objects\oo_object {
	
    public $flag = '';
    
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
	   $this->flag .= "BINT(".$from."=>$to)";    
	}
	
	public function parentint_changed($from,$to) {
	    $this->flag .= "AINT($from=>$to)";	    
	}
	
	public function parentchar_changing($from,$to) {
	    $this->flag .= "BCHAR($from=>$to)";
	    $this->parentint++;
	}
	
	public function parentchar_changed($from,$to) {
	    $this->flag .= "ACHAR($from=>$to)";
	}

	public function parentfloat_changing($from,$to) {
	    $this->flag .= "BFLOAT($from=>$to)";
	    $this->parentint--;
	}
	
	public function parentfloat_changed($from,$to) {
	    $this->flag .= "AFLOAT($from=>$to)";
	    if ($this->trigger_exception) {
	       $this->parentint--; // Exception
	    }
	}
	
	public function parentobject_changed($changed_fields) {
	    if (is_array($changed_fields)) {
    	    foreach ($changed_fields as $field => list($from,$to)) {
    	        $this->flag .= "AOBJECT($field:$from=>$to)";
    	    }
	    }
	}
	
	public function parentsarray_changed($new,$deleted) {
	    $this->flag .= "SARRAY(NEW:";
        foreach ($new as $entry) {
	        $this->flag .= "$entry ";
	    }
	    $this->flag .= "REMOVED:";
	    foreach ($deleted as $entry) {
	        $this->flag .= "$entry ";
	    }
	}
	
	public function parentoarray_changed($new,$deleted,$changed) {
	    $this->flag .= "OARRAY(NEW:";
	    foreach ($new as $entry) {
	        $this->flag .= $entry->dummyint." ";
	    }
	    $this->flag .= "REMOVED:";
	    foreach ($deleted as $entry) {
	        $this->flag .= $entry->dummyint." ";
	    }
	    $this->flag .= "CHANGED:";
	    foreach ($changed as $entry => $changes) {
	        foreach ($changes as $field => list($from,$to)) {
	            $this->flag .= $field."[$from=>$to]";	            
	        }
	    }
	}
	
}

