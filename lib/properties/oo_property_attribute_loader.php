<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_attribute_loader extends oo_property {
	
	protected $type = 'attribute_loader';
	
	protected $features = ['loader'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function load(int $id) {
	    $values = DB::table('attributevalues')->join('attributes','attributevalues.attribute_id','=','attributes.id')->
	              where('attributevalues.object_id','=',$this->owner->get_id())->get();
	    foreach ($values as $value) {
	        $attribute_name = $value->name;   
	        $this->owner->$attribute_name = $value->value;
	    }	    
	}

}