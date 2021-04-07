<?php namespace Sunhill\ORM\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_calculated extends storagemodule_base {
    
    public function load(int $id) {
        $values = DB::table('caching')->where('object_id','=',$id)->get();
        if (empty($values)) {
            return;
        }
        foreach ($values as $value) {
            $this->storage->entities[$value->fieldname] = $value->value;
        }
        return $id;
    }
    
    public function insert(int $id) {
        $inserts = [];
        $properties = $this->storage->filter_storage('calculated');
        foreach ($properties as $property=>$value) {
                $inserts[] = ['object_id'=>$id,'value'=>is_null($value)?'null':$value,'fieldname'=>$property];
        }
        DB::table('caching')->insert($inserts);
        return $id;
    }
    
    public function update(int $id) {
        $properties = $this->storage->filter_storage('calculated');
        foreach ($properties as $property=>$value) {
            DB::table('caching')->where('object_id',$id)->where('fieldname',$property)->update(['value'=>is_null($value['TO'])?'null':$value['TO']]);
        }
        return $id;
    }
    
    public function delete(int $id) {
        DB::table('caching')->where('object_id','=',$id)->delete();
        return $id;
    }
}
