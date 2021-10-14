<?php 
/**
 * @file StorageModuleMysqlTags.php
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */


namespace Sunhill\ORM\Storage;

use Illuminate\Support\Facades\DB;

/**
 * Storagemodul für die Verwaltung von Tagassoziationen. Die Methoden manipulieren dabei die 
 * Datenbanktabelle tagobjectassigns welches im folgenden als assoziative Tabelle bezeichnet wird. 
 * Diese legt die Verknüpfung zwischen Objekten und Tags im Sinne einer n:m Tabelle fest.
 * @author Klaus
 *
 */
class StorageModuleMySQLTags extends StorageModuleBase 
{
    
    /**
     * Läd sämtliche Tags aus der assoziativen Liste in das Storage
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::load()
     */
    public function load(int $id) 
    {
        $assigns = DB::table('tagobjectassigns')->where('container_id','=',$id)->get();
        if (empty($assigns)) {
            return;
        }
        foreach ($assigns as $assign) {
            $this->storage->entities['tags'][] = $assign->tag_id;
        }
        return $id;
    }
    
    /**
     * Fügt alle Tags zur assoziativen Liste hinzu
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::insert()
     */
    public function insert(int $id) 
    {
        if (is_null($this->storage->tags)) {
            return $id;
        }
        $this->insert_list($id,$this->storage->tags);
        return $id; 
    }
    
    /**
     * Gemeinsame Methode für insert() und update() zum hinzfügen einer Liste von Tags zur
     * Assoziativen Liste
     * @param int $id
     * @param array $list
     */
    protected function insert_list(int $id,array $list) 
    {
        $inserts = [];
        foreach ($list as $tag) {
            $inserts[] = ['container_id'=>$id,'tag_id'=>$tag];
        }
        DB::table('tagobjectassigns')->insert($inserts);        
    }
    
    /**
     * Update der Assoziativtabelle mit den hinzuzufügenden und zu löschenden Tags
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::update()
     */
    public function update(int $id) 
    {
        if (is_null($this->storage->tags)) {
            return $id;
        }
        if (!empty($this->storage->tags['ADD'])) {
            $this->insert_list($id,$this->storage->tags['ADD']);
        }
        if (!empty($this->storage->tags['DELETE'])) {
            DB::table('tagobjectassigns')->where('container_id','=',$id)->whereIn('tag_id', $this->storage->tags['DELETE'])->delete();            
        }
        return $id;
    }
    
    /**
     * Löscht die zu dem Objekt mit der ID $id gehörenden Tags aus der Assoziativliste
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::delete()
     */
    public function delete(int $id) 
    {
        DB::table('tagobjectassigns')->where('container_id','=',$id)->delete();
        return $id;
    }
    
}
