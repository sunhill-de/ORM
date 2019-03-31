<?php

namespace Sunhill;

use Illuminate\Support\Facades\Log;

define('LL_DEBUG',-1);
define('LL_INFO',-2);
define('LL_NOTICE',-3);
define('LL_WARNING',-4);
define('LL_ERROR',-5);
define('LL_CRTITICAL',-6);
define('LL_ALERT',-7);
define('LL_EMERGENCY',-8);

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
        if (!$this->check_loglevel(LL_DEBUG)) {
            return;
        }
        Log::debug($message);
    }
    
    protected function info(string $message) {
        if (!$this->check_loglevel(LL_INFO)) {
            return;
        }
        Log::info($message);
    }
    
    protected function notice(string $message) {
        if (!$this->check_loglevel(LL_NOTICE)) {
            return;
        }
        Log::notice($message);
    }
    
    protected function warning(string $message) {
        if (!$this->check_loglevel(LL_WARNING)) {
            return;
        }
        Log::warning($message);
    }
    
    protected function error(string $message) {
        if (!$this->check_loglevel(LL_ERROR)) {
            return;
        }
        Log::error($message);
    }
    
    protected function critical(string $message) {
        if (!$this->check_loglevel(LL_CRITICAL)) {
            return;
        }
        Log::critical($message);
    }
    
    protected function alert(string $message) {
        if (!$this->check_loglevel(LL_ALERT)) {
            return;
        }
        Log::alert($message);
    }
    
    protected function emergency(string $message) {
        if (!$this->check_loglevel(LL_EMERGENCY)) {
            return;
        }
        Log::emergency($message);
    }
    
    private function check_loglevel(int $requested) {
        if ($requested >= $this->loglevel) {
            return true;
        } else {
            return false;
        }
    }
}