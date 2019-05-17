<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_calculated extends oo_property_field {
	
	protected $type = 'calculated';

	protected $features = ['complex','calculated'];
	
	protected $readonly = true;
	
	public function get_dirty() {
	    return true;
	}
	
	public function set_value($value) {
	    throw new \Sunhill\Objects\ObjectException("Versuch ein Calulate-Field zu beschreiben");
	}
	public function &get_value() {
	    if (!$this->initialized) {
	        $method_name = 'calculate_'.$this->name;
	        $this->value = $this->owner->$method_name();
	    }
        return $this->value;
	}
	
	public function load(int $id) {
        $values = DB::table('caching')->select('value')->where('object_id','=',$id)->where('fieldname','=',$this->name)->first();	   
	    $this->value = $values->value;
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    DB::table('caching')->where('object_id','=',$id)->where('fieldname','=',$this->name)->update(['value'=>$this->get_value()]);
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	        DB::table('caching')->insert(['object_id'=>$id,'fieldname'=>$this->name,'value'=>$this->get_value()]);
	}

	public function get_where($relation,$value) {
	    if ($relation == 'begins with') {
	        return "value like '$value%'";
	    } else if ($relation == 'ends with') {
	        return "value like '%$value'";
	    } else if ($relation == 'consists') {
	        return "value like '%$value%'";
	    } else {
	        return "value ".$relation."'".$value."'";
	    }
	}
	
	
}