<?php

namespace Sunhill\ORM\Console;

use Illuminate\Console\Command;
use Sunhill\ORM\Facades\Classes;

class FlushCaches extends Command
{
    protected $signature = 'sunhill:flush_cache';
    
    protected $description = 'Flushes the sunhill caches';
    
    public function handle()
    {
        $this->info('Rebuilding objects cache...');
        Classes::create_cache();
        $this->info('Rebuilding objects cache finished');
    }
}
