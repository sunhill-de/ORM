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
	
	protected function setup_hooks() {
	    $this->add_hook('UPDATING_PROPERTY','parentint_changing','parentint');
	    $this->add_hook('UPDATED_PROPERTY','parentint_changed','parentint');
	    $this->add_hook('UPDATING_PROPERTY','parentchar_changing','parentchar');
	    $this->add_hook('UPDATED_PROPERTY','parentchar_changed','parentchar');
	    $this->add_hook('UPDATING_PROPERTY','parentfloat_changing','parentfloat');
	    $this->add_hook('UPDATED_PROPERTY','parentfloat_changed','parentfloat');
	    
	}
	
	public function parentint_changing($change) {
	    self::$flag .= "BINT(".$change['FROM']."=>".$change['TO'].")";    
	}
	
	public function parentint_changed($change) {
	    self::$flag .= "AINT(".$change['FROM']."=>".$change['TO'].")";
	}
	
	public function parentchar_changing($change) {
	    self::$flag .= "BCHAR(".$change['FROM']."=>".$change['TO'].")";
	    $this->parentint++;
	}
	
	public function parentchar_changed($change) {
	    self::$flag .= "ACHAR(".$change['FROM']."=>".$change['TO'].")";
	}

	public function parentfloat_changing($change) {
	    self::$flag .= "BFLOAT(".$change['FROM']."=>".$change['TO'].")";
	    $this->parentint--;
	}
	
	public function parentfloat_changed($change) {
	    self::$flag .= "AFLOAT(".$change['FROM']."=>".$change['TO'].")";
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
	    self::$flag .= "OARRAY(";
	    if (!empty($new)) {
	        self::$flag .= "NEW:";
	        foreach ($new as $entry) {
	            if (!is_null($entry)) {
	               self::$flag .= $entry->dummyint;
	            }
	        }
	        self::$flag .= ")";
	    }
	    if (!empty($deleted)) {
	        self::$flag .= "REMOVED:";
	        foreach ($deleted as $entry) {
	            if (!is_null($entry)) {
	                self::$flag .= $entry->dummyint;
	            }
	        }
	        self::$flag .= ")";
	    }	    
	}
	
}

