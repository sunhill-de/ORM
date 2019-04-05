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
	}

	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function updated(int $id) {
	}

	public function deleted(int $id) {
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