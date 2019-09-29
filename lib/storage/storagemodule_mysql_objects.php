<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

/**
 * Dieses Modul kümmert sich darum, Objektreferenzen in eine Mysql-Datenbank zu schreiben
 * @author Klaus
 *
 */
class storagemodule_mysql_objects extends storagemodule_base {
    
    /**
     * Läd die Objektreferenzen des übergebenen Objektes und speichert diese im Storage-Objekt
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::load()
     */
    public function load(int $id) {
        $references = DB::table('objectobjectassigns')->where('container_id','=',$id)->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if ($this->storage->get_caller()->get_property($reference->field)->has_feature('array')) {
                if (!isset($this->storage->entities[$reference->field])) {
                    $this->storage->entities[$reference->field] = [];
                }
                $this->storage->entities[$reference->field][$reference->index] = $reference->element_id;
            } else {
                $this->storage->entities[$reference->field] = $reference->element_id;
            }
        }
        return $id;
    }
    
    /**
     * Speichert aus dem Storage die Objektrefernzen in der Datenbank
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::insert()
     */
    public function insert(int $id) {
        $inserts = [];
        $properties = $this->storage->get_caller()->get_properties_with_feature('objectid');
        foreach ($properties as $property) {
            $fieldname = $property->get_name();
            if (is_array($this->storage->$fieldname)) {
                foreach ($this->storage->$fieldname as $index => $element) {
                    $inserts[] = ['container_id'=>$id,'element_id'=>$element,'field'=>$fieldname,'index'=>$index];
                }
            } else {
                $inserts[] = ['container_id'=>$id,'element_id'=>$this->storage->$fieldname,'field'=>$fieldname,'index'=>0];
            }
        }
        DB::table('objectobjectassigns')->insert($inserts);
        return $id;
    }
    
    /**
     * Bringt die Objektreferenzen in der Datenbank auf den neusten Stand
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::update()
     */
    public function update(int $id) {
        $inserts = [];
        $properties = $this->storage->get_caller()->get_properties_with_feature('objectid');
        foreach ($properties as $property) {
            $fieldname = $property->get_name();
            if (isset($this->storage->entities[$fieldname])) {
                DB::table('objectobjectassigns')->where('container_id',$id)->where('field',$fieldname)->delete();
                if (is_array($this->storage->$fieldname)) {
                    foreach ($this->storage->$fieldname as $index => $element) {
                        $inserts[] = ['container_id'=>$id,'element_id'=>$element,'field'=>$fieldname,'index'=>$index];
                    }
                } else {
                    $inserts[] = ['container_id'=>$id,'element_id'=>$this->storage->$fieldname,'field'=>$fieldname,'index'=>0];
                }
            }
        }
        DB::table('objectobjectassigns')->insert($inserts);
        return $id;
    }
    
    /**
     * Löscht aus der Datenbank die Objektreferenzen
     * {@inheritDoc}
     * @see \Sunhill\Storage\storagemodule_base::delete()
     */
    public function delete(int $id) {
        DB::table('objectobjectassigns')->where('container_id','=',$id)->delete();
        return $id;
    }
}
