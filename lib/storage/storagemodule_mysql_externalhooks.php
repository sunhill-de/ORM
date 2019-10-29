<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_externalhooks extends storagemodule_base {
    
    public function load(int $id) {
        $hooks = DB::table('externalhooks')->where('container_id','=',$id)->get();
        if (empty($hooks)) {
            return;
        }
        foreach($hooks as $hook) {
            $line = [];
            foreach ($hook as $key => $value) {
                $line[$key] = $value;
            }
            $this->storage->entities['externalhooks'][] = $line;
        }
        return $id;
    }
    
    public function insert(int $id) {
        $lines = [];
        foreach ($this->storage->entities['externalhooks'] as $hook) {
            $line = [
                'container_id'=>$id,
                'target_id'=>$hook['target'],
                'action'=>$hook['action'],
                'subaction'=>$hook['subaction'],
                'hook'=>$hook['hook'],
                'payload'=>$hook['payload']
            ];
            $lines[] = $line;
        }
        DB::table('externalhooks')->insert($lines);
        return $id;
    }
    
    public function update(int $id) {
        return $id;
    }
    
    public function delete(int $id) {
        DB::table('externalhooks')->where('container_id',$id)->orWhere('target_id',$id)->delete();
        return $id;
    }
}
