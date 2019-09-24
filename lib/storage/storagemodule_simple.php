<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

/**
 * Die Klasse storage_load sammelt die Daten aus der Datenbank oder einem wie auch immer gearteten Storage
 * Die Objekte wiederrum laden sich dann die Daten in normalisierter Form aus diesem Storage
 * Der Vorteil des Storage ist dabei, dass die Repräsentation der Daten gekapselt von den Objekten ist und nur
 * durch eine Anpassung des Storagesobjektes angepasst werden kann.
 * @author klaus
 *
 */
class storagemodule_simple extends storagemodule_base {
    
    /**
    public function prepare_load(int $id) {
        // Macht gar nichts
    }
    
    /**
     * Läd die Simple-Fields der Objekte
     * @param int $id
     */
    public function load_object(int $id) {
        $this->entities = ['id'=> $id,'tags'=>[],'attributes'=>[],'externalhooks'=>[]];
        $this->load_parentchain();
        $this->load_objects();
        $this->load_strings();
        $this->load_attributes();
        $this->load_tags();
        $this->load_calculated();
        $this->load_externalhooks();
     }
    
    private function load_parentchain() {
        foreach ($this->get_inheritance() as $inheritance) {
            $table = $inheritance::$table_name;
            $result = DB::table($table)->where('id','=',$this->entities['id'])->first();
            if (!empty($result)) {
                foreach ($result as $name => $value) {
                    $this->entities[$name] = $value;
                }
            } else {
                throw new StorageException("Eine ID '".$this->entities['id']."' gibt es in der Tabelle '$table' nicht.");
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
            $this->entities['tags'][] = $assign->tag_id;
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
    
}
