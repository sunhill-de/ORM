<?php

/**
 * @file FlushCaches.php
 * A command for the artisan interface that clears all ORM specific caches (at this time only the class cache)
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-08-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/ORMCheckTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Console;

use Illuminate\Console\Command;
use Sunhill\ORM\Facades\Classes;

class FlushCaches extends Command
{
    protected $signature = 'sunhill:flush_cache';
    
    protected $description = 'Flushes the sunhill caches';
    
    public function handle()
    {
        $this->info(__('Rebuilding objects cache...'));
        Classes::create_cache();
        $this->info(__('Rebuilding objects cache finished'));
    }
}
