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
                 'value_id'=>$value->id,
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
        foreach ($this->storage->entities['attributes'] as $attribute) {
            $insert = ['attribute_id'=>$attribute['attribute_id'],
                          'object_id'=>$id,
                          'value'=>$attribute['value'],
                          'textvalue'=>$attribute['textvalue']];
            $this->storage->entities['attributes'][$attribute['name']]['id'] = 
                   DB::table('attributevalues')->insertGetId($insert);
        }
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
