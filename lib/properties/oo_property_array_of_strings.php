<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_array_of_strings extends oo_property_arraybase {
	
	protected $type = 'array_of_strings';
	
	protected $features = ['object','complex','array','strings'];
	
	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    $this->set_dirty(false);
	    DB::table('stringobjectassigns')->where([['container_id','=',$id],
	        ['field','=',$this->get_name()]])->delete();
	        $this->inserted($id);
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    foreach ($this->value as $index => $value) {
	        $model = new \App\stringobjectassign();
	        $model->container_id = $id;
	        $model->element_id = $value;
	        $model->field = $this->get_name();
	        $model->index = $index;
	        $model->save();
	    }
	}
	
	public function get_table_name($relation,$where) {
	    return "";
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "";
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    switch ($relation) {
	        case 'has':
	            return "a.id in (select x.container_id from stringobjectassigns as x where x.element_id = ".
	   	            $this->escape($value)." and x.field = '".$this->get_name()."')";
	        case 'has not':
	            return "a.id not in (select x.container_id from stringobjectassigns as x where x.element_id = ".
	   	            $this->escape($value)." and x.field = '".$this->get_name()."')";
	        case 'one of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' or ';
	                }
	                $first = false;
	                $result .= "x.element_id = $single_value";
	            }
	            return "a.id in (select x.container_id from stringobjectassigns as x where (".$result.")".
	   	               " and x.field = '".$this->get_name()."')";
	        case 'all of':
	            $result = '';
	            $first = true;
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' and ';
	                }
	                $first = false;
	                $result .= "a.id in (select xx.container_id from stringobjectassigns as xx ".
	   	                "where xx.element_id = $single_value and xx.field = '".$this->get_name()."')";
	            }
	            return $result; break;
	        case 'none of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' or ';
	                }
	                $first = false;
	                $result .= "x.element_id = $single_value";
	            }
	            return "a.id not in (select x.container_id from stringobjectassigns as x where (".$result.")".
	   	            " and x.field = '".$this->get_name()."')"; break;
	        case 'empty':
	            return "a.id not in (select xx.container_id from stringobjectassigns as xx where ".
	   	               "xx.field = '".$this->get_name()."')";
	    }
	}
		
	
}