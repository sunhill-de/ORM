<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_tags extends oo_property_arraybase {
	
	protected $type = 'tags';
	
	protected $features = ['tags','array'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function stick(\Sunhill\Objects\oo_tag $tag) {
	    foreach ($this->value as $listed) {
	        if ($listed->get_fullpath() === $tag->get_fullpath()) {
	            return $this; // Gibt es schon
	        }
	    }
	    $this->value[] = $tag;
	    $this->set_dirty(true);
	}
	
	public function remove($tag) {
	    for ($i=0;$i<count($this->value);$i++) {
	        if ($this->value[$i]->get_fullpath() === $tag->get_fullpath()) {
	            array_splice($this->value,$i,1);
	            $this->set_dirty(true);
	            return $this;
	        }	        
	    }	    
	    throw new PropertyException("Das zu löschende Tag '".$tag->get_fullpath()."' ist gar nicht gesetzt");
	}
	
	public function load(int $id) {
	    $assigns = \App\tagobjectassign::where('container_id','=',$id)->get();
	    foreach ($assigns as $assign) {
	        $tag = new \Sunhill\Objects\oo_tag($assign->tag_id);
	        $this->stick($tag);
	    }
	    $this->set_dirty(false);
	    $this->initialized = true;
	    $this->shadow = $this->value;
	}

	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    foreach ($this->value as $tag) {
	        $tagid = $tag->get_id();
	        DB::statement("insert ignore into tagobjectassigns (container_id,tag_id) values ($id,$tagid)");
	    }
	}

	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function updated(int $id) {
	    $this->deleted($id);
	    if (count($this->value) > 0) {
    	    foreach ($this->value as $tag) {
    	        $tagid = $tag->get_id();
    	        DB::statement("insert ignore into tagobjectassigns (container_id,tag_id) values ($id,$tagid)");
    	    }
	    }
	}

	public function deleted(int $id) {
	    DB::statement("delete from tagobjectassigns where container_id = $id");
	    
	}
	/**
	 * Speichert eine einzelne Referenz eines Tags in der Datenbank ab
	 * @todo Hier besteht Optimierungpotential für zusammengefassten Datenbankanfragen
	 * @param oo_tag $tag
	 */
	private function store_tag(oo_tag $tag,$id) {
	}
	
	public function get_table_name($relation,$where) {
	    return "tagcache";
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return $this;
	}
	
	public function get_special_join($letter) {
	    return " inner join tagobjectassigns as zz on zz.container_id = a.id inner join tagcache as $letter on zz.tag_id = $letter.tag_id";     
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    switch ($relation) {
	        case 'has':
	            return "$letter.name = ".$this->escape($value); break; 
	        case 'has not':
	            return "$letter.name <> ".$this->escape($value); break;
	        case 'one of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' or ';
	                }
	                $first = false;
	                $result .= "$letter.name = $single_value";
	            }
	            return $result; break;
	        case 'all of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' and ';
	                }
	                $first = false;
	                $result .= "$letter.name = $single_value";
	            }
	            return $result; break;
	        case 'none of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' and ';
	                }
	                $first = false;
                    $result .= "not $letter.name = $single_value";
	            }
	            return $result; break;
	    }
	}
	
}