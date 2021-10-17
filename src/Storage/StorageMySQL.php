<?php

/**
 * @file StorageMySQL.php
 * The basic class for storages that use mySQL
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
 * Die Klasse StorageMySQL ist die Standardklasse für Storages. Sie definiert zusätzlich nur die mysql Module für
 * die einzelnen Entity-Klassen
 * @author lokal
 *
 */
class StorageMySQL extends StorageBase  
{
    
    protected $modules = ['MySQLSimple','MySQLObjects','MySQLStrings','MySQLCalculated',
                          'MySQLTags','MySQLExternalHooks','MySQLAttributes'];
    

    public function executeNeedIDQueries()
    {
        if (empty($this->entities['needid_queries'])) {
            return;
        }
        foreach ($this->entities['needid_queries'] as $query) {
            $query['fixed'][$query['id_field']] = $this->caller->getID();
            DB::table($query['table'])->insert($query['fixed']);
        }
    }
}
