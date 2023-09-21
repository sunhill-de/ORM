<?php

/**
 * @file MigrateObjects.php
 * an artisan command that creates the tables for the orm objects
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: complete
 * Tests: tests/Unit/Console/MigrateObjectsTest.php
 * Coverage: unknown
 * @todo localization
 */

namespace Sunhill\ORM\Console;

use Illuminate\Console\Command;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Collections;

class MigrateObjects extends Command
{
    protected $signature = 'sunhill:migrate';
    
    protected $description = 'Migrates the provided objects';
    
    public function __construct() 
    {
        parent::__construct();
        $this->description = __('Migrates the provided objects');
    }
    
    public function handle()
    {
        $this->info(__('Migrating objects...'));
        
        $classes = Classes::getAllClasses();
        if (!empty($classes)) {
            foreach($classes as $name => $infos) {
                $this->info(__('Migrating :name: ',['name'=>$name]));
                Classes::migrateClass($name);
            }
        }
        $this->info(__('Migrating collections...'));
        Collections::migrateCollections();
        $this->info(__('Migrating finished.'));
    }
}
