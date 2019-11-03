<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

/**
 * Das MYSQL-Storagemodul für externe Hooks
 * @author lokal
 *
 */
class storagemodule_mysql_externalhooks extends storagemodule_base {
    
    /**
     * Läd aus der Datenbanktabelle externalhooks als zur id $id passenden externen Hooks und
     * schreibt diese in das entities Feld des Storages.
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::load()
     */
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
    
    /**
     * Liest aus dem entity-Feld des Storages die Informationen über externe Hooks und fügt diese in die
     * Datenbanktabelle externalhooks ein. 
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::insert()
     */
    public function insert(int $id) {
        $lines = [];
        if (empty($this->storage->entities['externalhooks'])) {
            return $id;
        }
        foreach ($this->storage->entities['externalhooks'] as $hook) {
            $line = [
                'container_id'=>$id,
                'target_id'=>$hook['target_id'],
                'action'=>$hook['action'],
                'subaction'=>$hook['subaction'],
                'hook'=>$hook['hook'],
                'payload'=>(is_null($hook['payload'])?'':$hook['payload'])
            ];
            $lines[] = $line;
        }
        DB::table('externalhooks')->insert($lines);
        return $id;
    }
    
    /**
     * Liest aus dem entity-Feld des Storages die Subfelder für externe Hooks NEW und REMOVED
     * Die neuen werden eingefügt, die alten gelöscht.
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::update()
     */
    public function update(int $id) {
        if (empty($this->storage->entities['externalhooks'])) {
            return $id;
        }
        foreach ($this->storage->entities['externalhooks']['REMOVED'] as $hook) {
           DB::table('externalhooks')
                ->where('container_id',$id)
                ->where('target_id',$hook['target_id'])
                ->where('action',$hook['action'])
                ->where('subaction',$hook['subaction'])
                ->where('hook',$hook['hook'])->delete();
        }
        foreach ($this->storage->entities['externalhooks']['NEW'] as $hook) {
           DB::table('externalhooks')->insert(
               [
                   'container_id'=>$id,
                   'target_id'=>$hook['target_id'],
                   'action'=>$hook['action'],
                   'subaction'=>$hook['subaction'],
                   'hook'=>$hook['hook'],
                   'payload'=>$hook['payload']                   
               ]
               ); 
        }
        return $id;
    }
    
    /**
     * Löscht aus der Tabelle externalhooks alle Referenzen auf die übergebene ID $id
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::delete()
     */
    public function delete(int $id) {
        DB::table('externalhooks')->where('container_id',$id)->orWhere('target_id',$id)->delete();
        return $id;
    }
}
