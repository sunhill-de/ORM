<?php namespace Sunhill\ORM\Storage;

use Illuminate\Support\Facades\DB;

/**
 * Dieses Modul kümmert sich darum, Objektreferenzen in eine Mysql-Datenbank zu schreiben
 * @author Klaus
 *
 */
class storagemodule_mysql_objects extends StorageModuleBase {
    
    /**
     * Läd die Objektreferenzen des übergebenen Objektes und speichert diese im Storage-Objekt
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::load()
     */
    public function load(int $id) {
        $references = DB::table('objectobjectassigns')->where('container_id','=',$id)->orderBy('index','asc')->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if ($this->storage->getCaller()->getProperty($reference->field)->hasFeature('array')) {
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
     * @see \Sunhill\ORM\Storage\StorageModuleBase::insert()
     */
    public function insert(int $id) {
        $inserts = [];
        $properties = $this->storage->getCaller()->get_properties_with_feature('objectid');
        foreach ($properties as $property) {
            $fieldname = $property->getName();
            if (is_null($property->value)) {
                continue;
            }
            if (is_array($this->storage->$fieldname)) {
                foreach ($this->storage->$fieldname as $index => $element) {
                    if (empty($element)) {
                        $property->value->addNeedIDQuery('objectobjectassigns',['container_id'=>$id,'field'=>$fieldname,'index'=>$index],'element_id');   
                    } else {
                        $inserts[] = ['container_id'=>$id,'element_id'=>$element,'field'=>$fieldname,'index'=>$index];
                    }
                }
            } else {
                $element = $this->storage->$fieldname;
                if (empty($element)) {
                    if (is_a($property->value,'\\Sunhill\\ORM\\Objects\\ORMObject')) {
                        $property->value->addNeedIDQuery('objectobjectassigns',['container_id'=>$id,'field'=>$fieldname,'index'=>0],'element_id');
                    }
                } else {
                    $inserts[] = ['container_id'=>$id,'element_id'=>$element,'field'=>$fieldname,'index'=>0];
                }
            }
        }
        if (!empty($inserts)) {
            DB::table('objectobjectassigns')->insert($inserts);
        }
        return $id;
    }
    
    /**
     * Bringt die Objektreferenzen in der Datenbank auf den neusten Stand
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\StorageModuleBase::update()
     */
    public function update(int $id) {
        $properties = $this->storage->filterStorage('objectid');
        if (empty($properties)) {
            return $id;
        }
        $inserts = [];
        foreach ($properties as $property=>$diff) {
            if (!isset($diff['ADD'])) {
                // Es ist nur ein einfaches Objektfeld
                if (is_null($diff['TO'])) {
                    DB::table('objectobjectassigns')
                    ->where('container_id',$id)
                    ->where('field',$property)
                    ->where('index',0)->delete();
                } else {
                    DB::table('objectobjectassigns')->updateOrInsert([
                        'container_id'=>$id,
                        'field'=>$property,
                        'index'=>0],[
                            'element_id'=>$diff['TO']                        
                        ]);
                }
            } else {            
                if (!empty($diff['ADD'])) {
                    foreach ($diff['ADD'] as $index=>$value) {
                        $inserts[] = ['container_id'=>$id,'element_id'=>$value,'field'=>$property,'index'=>$index];
                    }
                }
                if (!empty($diff['DELETE'])) {
                    DB::table('objectobjectassigns')->where('container_id','=',$id)
                    ->whereIn('element_id',$diff['DELETE'])->delete();
                }
            }
        }
        DB::table('objectobjectassigns')->insert($inserts);
        return $id;
    }
    
    /**
     * Löscht aus der Datenbank die Objektreferenzen
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\StorageModuleBase::delete()
     */
    public function delete(int $id) {
        DB::table('objectobjectassigns')->where('container_id','=',$id)->delete();
        return $id;
    }
    /**
     * Löscht die höhergestellten Tabellen
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\StorageModuleBase::degrade()
     */
    public function degrade(int $id,array $degration_info) {
        $properties = $this->storage->filterStorage('objectid');
        foreach ($properties as $property=>$payload) {
                DB::table('objectobjectassigns')
                    ->where('container_id',$id)
                    ->where('field',$property)->delete();
        }
        return $id;
    }
}
