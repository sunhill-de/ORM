<?php

namespace Sunhill\ORM\Console;

use Illuminate\Console\Command;
use Sunhill\ORM\Facades\Classes;

class MigrateObjects extends Command
{
    protected $signature = 'sunhill:migrate';
    
    protected $description = 'Migrates the provides objects';
    
    public function handle()
    {
        $this->info('Migrating objects...');
        
        $this->info('Rebuilding objects cache...');
        Classes::create_cache();
        $classes = Classes::get_all_classes();
        if (!empty($classes)) {
            foreach($classes as $name => $infos) {
                $this->info('Migrating: '.$name);
                $namespace = $infos->class;
                $namespace::migrate();
            }
        }
        $this->info('Migrating finished.');
    }
}
