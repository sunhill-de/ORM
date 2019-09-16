<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storage_insert extends storage_base {
    
    public function store_object() {
        $this->store_core();
        $this->store_the_rest();
        return $this->entities['id'];
    }
    
    private function store_core() {
        $id = DB::table('objects')->insertGetId(['classname'=>$this->entities['classname'],
            'created_at'=>DB::raw('now()'),
            'updated_at'=>DB::raw('now()')
        ]);
        $this->entities['id'] = $id;
    }
    
    private function store_the_rest() {
        foreach ($this->entities as $key => $value) {
            switch ($key) {
                case 'xx_objects':
                    $this->store_objects($value);
                    break;
                case 'xx_strings':
                    $this->store_string($value);
                case 'xx_tags':
                    $this->store_tags($value);
                    break;
                case 'xx_attributes':
                    $this->store_attributes($value);
                    break;
                case 'xx_calculated':
                    $this->store_calculated($value);
                    break;
                case 'xx_externalhooks':
                    $this->store_externalhooks($value);
                    break;
                case 'id':
                case 'classname':
                case 'objects':
                    continue;
                default:
                    $this->store_table($key,$value);
            }
        }
    }
    
    private function store_table($tablename,$values) {
        $values['id'] = $this->entities['id'];
        DB::table($tablename)->insert($values);
    }
        
    private function store_objects($values) {
        $inserts = [];
        foreach ($values as $index => $value) {
            if (!empty($value)) {
                $inserts[] = ['container_id'=>$this->entities['id'],'element_id'=>$value->get_id(),'index'=>$index];
            }
        }
        DB::table('objectobjectassigns')->insert($inserts);
    }
    
    private function store_strings($values) {
        $inserts = [];
        foreach ($values as $index => $value) {
            $inserts[] = ['container_id'=>$this->entities['id'],'element_id'=>$value,'index'=>$index];
        }
        DB::table('stringobjectassigns')->insert($inserts);
    }
    
    private function store_attributes($values) {
    }
    
    private function store_tags($values) {
    }
    
    private function store_calculated($values) {
        $inserts = [];
        foreach ($values as $index => $value) {
            if (!is_null($value)) {
                $inserts[] = ['object_id'=>$this->entities['id'],'value'=>$value,'fieldname'=>$index];
            }
        }
        DB::table('caching')->insert($inserts);
        
    }
    
    private function store_externalhooks($values) {
    }
    
    public function set_subvalue($array,$name,$value) {
        if (isset($this->entities[$array])) {
            $this->entities[$array][$name] = $value;
        } else {
            $this->entities[$array] = [$name=>$value];
        }
    }
}
