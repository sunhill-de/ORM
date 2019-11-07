<?php

namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

/** 
 * Die Klasse storage_mysql ist die Standardklasse für Storages. Sie definiert zusätzlich nur die mysql Module für
 * die einzelnen Entity-Klassen
 * @author lokal
 *
 */
class storage_mysql extends storage_base  {
    
    protected $modules = ['mysql_simple','mysql_objects','mysql_strings','mysql_calculated',
                          'mysql_tags','mysql_externalhooks','mysql_attributes'];
    

    protected function execute_need_id_queries()
    {
        foreach ($this->needid_queries as $query) {
            $query['fixed'][$query['id_field']] = $this->get_id();
            DB::table($query['table'])->insert($query['fixed']);
        }
    }
}
