<?php namespace Sunhill\Storage;

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
    }
    
}
