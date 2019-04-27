<?php

namespace Sunhill\Test;

class ts_thirdlevelchild extends ts_secondlevelchild {
    public static $table_name = 'thirdlevelchildren';
    
	protected function setup_properties() {
		parent::setup_properties();
		$this->integer('childchildint')->set_model('thirdlevelchild');;
	}
	
	public function post_promotion($from) {
	    if (is_a($from,'Sunhill\Test\ts_secondlevelchild')) {
	        $this->childchildint = $this->childint * 2;
	    } elseif (is_a($from,'Sunhill\Test\ts_passthru')) {	        
	        $this->childint = 2;
	        $this->childchildint = $this->childint * 2;	        
	    } else {
	        throw new \Exception("OOPS");
	    }
	}
}

