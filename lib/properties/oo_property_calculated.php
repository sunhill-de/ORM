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
	        //$this->initialized = true;
	    }
        return $this->value;
	}
		
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    $value = $this->get_value();
	    if (!is_null($value)) {
	       DB::table('caching')->where('object_id','=',$id)->where('fieldname','=',$this->name)->update(['value'=>$this->get_value()]);
	    }
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    $value = $this->get_value();
	    if (!is_null($value)) {
	        DB::table('caching')->insert(['object_id'=>$id,'fieldname'=>$this->name,'value'=>$this->get_value()]);
	    }
	}

	public function get_table_name($relation,$where) {
	    return 'caching';
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "on a.id = $letter.object_id";
	}
	
	protected function get_individual_where($relation,$value,$letter) {
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
	
	protected function is_allowed_relation(string $relation,$value) {
	    switch ($relation) {
	        case '=':
	        case '<':
	        case '>':
	        case '>=':
	        case '<=':
	        case '<>':
	        case 'begins with':
	        case 'ends with':
	        case 'consists':
	            return is_scalar($value); break;
	        case 'in':
	            return is_array($value); break;
	        default:
	            return false;
	    }
	}
	
}