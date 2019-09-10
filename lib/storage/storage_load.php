<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storage_load extends storage_base {
    
    protected $entities = [];
    
    public function load_object(int $id) {
        $this->entities = ['id'=> $id,'tags'=>[],'attributes'=>[],'externalhooks'=>[]];
        $this->load_core();
        $this->load_parentchain();
        $this->load_objects();
        $this->load_strings();
        $this->load_attributes();
        $this->load_tags();
        $this->load_calculated();
        $this->load_externalhooks();
     }
    
    private function load_core() {
        $core = DB::table('objects')->select('updated_at','created_at')->where('id','=',$this->entities['id'])->first();
        $this->entities['updated_at'] = $core->updated_at;
        $this->entities['created_at'] = $core->created_at;
    }
    
    private function load_parentchain() {
        foreach ($this->inheritance as $inheritance) {
            $table = $inheritance::$table_name;
            $result = DB::table($table)->where('id','=',$this->entities['id'])->first();
            if (!empty($result)) {
                foreach ($result as $name => $value) {
                    $this->entities[$name] = $value;
                }
            }
        }
    }
    
    private function load_objects() {
        $references = DB::table('objectobjectassigns')->where('container_id','=',$this->entities['id'])->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if ($this->caller->get_property($reference->field)->has_feature('array')) {
                if (!isset($this->entities[$reference->field])) {
                    $this->entities[$reference->field] = [];
                }
                $this->entities[$reference->field][$reference->index] = $reference->element_id;
            } else {
                $this->entities[$reference->field] = $reference->element_id;
            }
        }        
    }
    
    private function load_strings() {
        $references = DB::table('stringobjectassigns')->where('container_id','=',$this->entities['id'])->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if (!isset($this->entities[$reference->field])) {
                $this->entities[$reference->field] = [];
            }
            $this->entities[$reference->field][$reference->index] = $reference->element_id;
        }
    }
    
    private function load_attributes() {
        $values = DB::table('attributevalues')->join('attributes','attributevalues.attribute_id','=','attributes.id')->
        where('attributevalues.object_id','=',$this->entities['id'])->get();
        foreach ($values as $value) {
            $attribute_name = $value->name;
            $this->entities['attributes'][$attribute_name] = $value->value;
        }
        
    }
    
    private function load_tags() {
        $assigns = DB::table('tagobjectassigns')->where('container_id','=',$this->entities['id'])->get();
        if (empty($assigns)) {
            return;
        }
        foreach ($assigns as $assign) {
            $tag = new \Sunhill\Objects\oo_tag($assign->tag_id);
            $this->entities['tags'][] = $tag;
        }
        
    }
    
    private function load_calculated() {
        $values = DB::table('caching')->where('object_id','=',$this->entities['id'])->get();
        if (empty($values)) {
            return;
        }
        foreach ($values as $value) {
            $this->entities[$value->fieldname] = $value->value;
        }
    }
    
    private function load_externalhooks() {
        $hooks = DB::table('externalhooks')->where('container_id','=',$this->entities['id'])->get();
        if (empty($hooks)) {
            return;
        }
        foreach($hooks as $hook) {
            $line = [];
            foreach ($hook as $key => $value) {
                $line[$key] = $value;
            }
            $this->entities['externalhooks'][] = $line;
        }
    }
    
    public function get_entity(string $name) {
        if (!isset($this->entities[$name])) {
            return null;
        } else {
            return $this->entities[$name];
        }
    }
    
    public function __get(string $name) {
        return $this->get_entity($name);
    }
    
    public function get_id() {
        return $this->id;
    }
}
