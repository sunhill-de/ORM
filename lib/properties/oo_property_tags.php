<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_tags extends oo_property_arraybase {
	
	protected $type = 'tags';
	
	protected $features = ['tags','array'];
	
	public function stick($tag) {
	    if (is_int($tag)) {
	        $tag = \Sunhill\Objects\oo_tag::load_tag($tag);
	    }
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

	protected function &do_get_indexed_value($index) {
	    $value = $this->value[$index]->get_fullpath();
	    return $value;
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
	    $result = [];
	    foreach ($this->value as $tag) {
	        if (is_int($tag)) {
	            $result[] = $tag;
	        } else {
	            $result[] = $tag->get_id();
	        }
	    }
	    $storage->set_entity('tags',$result);
	}
	
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name)  {
	    if (empty($loader->entities['tags'])) {
	        return;
	    }
	    foreach ($loader->entities['tags'] as $tag) {
	        $this->stick($tag);
	    }
	}
	
	public function get_table_name($relation,$where) {
	    return "";
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "";
	}
	
	public function get_special_join($letter) {
	    return " inner join tagobjectassigns as zz on zz.container_id = a.id inner join tagcache as $letter on zz.tag_id = $letter.tag_id";     
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    switch ($relation) {
	        case 'has':
	            return "a.id in (select x.container_id from tagobjectassigns as x inner join tagcache as y on y.tag_id = x.tag_id where y.name = ".
	   	            $this->escape($value).")";
	        case 'has not':
	            return "a.id not in (select x.container_id from tagobjectassigns as x inner join tagcache as y on y.tag_id = x.tag_id where y.name = ".
	   	            $this->escape($value).")";
	        case 'one of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' or ';
	                }
	                $first = false;
	                $result .= "y.name = $single_value";
	            }
	            return "a.id in (select x.container_id from tagobjectassigns as x inner join tagcache as y on y.tag_id = x.tag_id where ".$result.")";
	        case 'all of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' and ';
	                }
	                $first = false;
	                $result .= "a.id in (select xx.container_id from tagobjectassigns as xx inner join tagcache as xy on xy.tag_id = xx.tag_id ".
	   	                       "where xy.name = $single_value)";
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
	                $result .= "y.name = $single_value";
	            }
	            return "a.id not in (select x.container_id from tagobjectassigns as x inner join tagcache as y on y.tag_id = x.tag_id where ".$result.")";
	    }
	}
	
}