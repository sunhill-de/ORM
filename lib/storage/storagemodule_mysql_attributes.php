<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_attributes extends storagemodule_base {
    
    public function load(int $id) {
        $values = DB::table('attributevalues')->join('attributes','attributevalues.attribute_id','=','attributes.id')->
        where('attributevalues.object_id','=',$id)->get();
        foreach ($values as $value) {
            $attribute_name = $value->name;
            $this->storage->entities['attributes'][$attribute_name] = $value->value;
        }
    }
    
}
