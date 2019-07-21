<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class AttributeException extends \Exception {}

class oo_property_attribute extends oo_property {
	
	protected $type = 'attribute';
	
	protected $features = ['attribute'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function load(int $id) {
	}

	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    $attribute = \Sunhill\Properties\oo_property_attribute::search($this->get_name());
	    $attributevalue = new \App\attributevalue();
	    $attributevalue->attribute_id = $attribute->id;
	    $attributevalue->object_id = $this->owner->get_id();
	    $attributevalue->value = $this->get_value();
	    $attributevalue->textvalue = '';
	    $attributevalue->save();
	}

	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function updated(int $id) {
	   $this->deleted($id);
	   $this->inserted($id);
	}

	public function deleted(int $id) {
	    $attribute = \Sunhill\Properties\oo_property_attribute::search($this->get_name());
	    DB::table('attributevalues')->where('object_id','=',$this->owner->get_id())
	                               ->where('attribute_id','=',$attribute->id)->delete();
	}

	// ============================ Statische Funktionen ===========================
	static public function search($name) {
	    $property = \App\attribute::where('name','=',$name)->first();
	    if (empty($property)) {
	        return false;
	    } else {
	        return $property;
	    }
	}
}