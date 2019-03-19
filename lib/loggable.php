<?php

namespace Sunhill;

use Illuminate\Support\Facades\Log;

define('LL_DEBUG',-1);
define('LL_INFO',-2);
define('LL_NOTICE',-3);

/**
 * Macht zur Zeit erstmal nichts, dient nur als gemeinsamer Vorfahre für die weiteren crawler-Klassen
 * @author klaus
 *
 */
class loggable extends base {
	
    private $loglevel;
    
    public function set_loglevel(int $loglevel) {
        $this->loglevel = $loglevel;
        return $this;
    }
    
    public function get_loglevel() {
        return $this->loglevel;
    }
    
    protected function debug(string $message) {
        if ($this->check_loglevel(LL_DEBUG)) {
            return;
        }
        Log::debug($message);
    }
    
    private function check_loglevel(int $requested) {
        if ($requested >= $this->loglevel) {
            return true;
        } else {
            return false;
        }
    }
}