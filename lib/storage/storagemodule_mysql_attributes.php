<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_attributes extends storagemodule_base {
    
    public function load(int $id) {
        $values = DB::table('attributevalues')->join('attributes','attributevalues.attribute_id','=','attributes.id')->
        where('attributevalues.object_id','=',$id)->get();
        foreach ($values as $value) {
            $attribute_name = $value->name;
            $this->storage->entities['attributes'][$attribute_name] = 
              [
                 'attribute_id'=>$value->attribute_id,
                 'object_id'=>$id,
                  'value'=>$value->value,
                  'textvalue'=>$value->textvalue,
                  'name'=>$value->name,
                  'allowedobjects'=>$value->allowedobjects,
                  'type'=>$value->type,
                  'property'=>$value->property
              ];
        }
        return $id;
    }
    
    public function insert(int $id) {
        if (! isset($this->storage->entities['attributes'])) {
            return $id;
        }
        $inserts = [];
        foreach ($this->storage->entities['attributes'] as $attribute) {
            $inserts[] = ['attribute_id'=>$attribute['attribute_id'],
                          'object_id'=>$id,
                          'value'=>$attribute['value'],
                          'textvalue'=>$attribute['textvalue']];
        }
        DB::table('attributevalues')->insert($inserts);
        return $id;
    }
    
    public function update(int $id) {
        if (! isset($this->storage->entities['attributes'])) {
            return $id;
        }
        foreach($this->storage->entities['attributes'] as $attribute) {
            $updates = ['attribute_id'=>$attribute['attribute_id'],
                'object_id'=>$id,
                'value'=>$attribute['value'],
                'textvalue'=>$attribute['textvalue']];            
            DB::table('attributevalues')->where('id',$attribute['id'])->update($updates);
        }
        return $id;
    }
    
    public function delete(int $id) {
       DB::table('attributevalues')->where('object_id','=',$id)->delete(); 
       return $id;
    }
}
