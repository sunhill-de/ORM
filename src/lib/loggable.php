<?php
/**
 * @file loggable.php
 * Definiert das Objekt loggable
 */

namespace Sunhill\ORM;

use Illuminate\Support\Facades\Log;

/**
 * Konstanten für die verschiedenen Loglevel
 * @var unknown
 */
define('LL_DEBUG',-1);
define('LL_INFO',-2);
define('LL_NOTICE',-3);
define('LL_WARNING',-4);
define('LL_ERROR',-5);
define('LL_CRTITICAL',-6);
define('LL_ALERT',-7);
define('LL_EMERGENCY',-8);

/**
 * Basisklasse für Klassen, die Ausgaben in das Framework-Log ausgeben können
 * Die Klasse bietet zum einen den sog. Loglevel, über den festgelegt wird ab welchem Dringlichkeitsgrad
 * die Einträge erfolgen sollen. 
 * Ansonsten für die standarisierten Dringlichkeitsstufen die jeweiligen Methoden:
 * - debug()
 * - info()
 * - notice()
 * - warning()
 * - error()
 * - critical()
 * - alert()
 * - emergency()
 * 
 * Diese Methoden übernehmen jeweils einen String, der die eigentliche Lognachricht darstellt
 * @author klaus
 */
class loggable extends base {
	
    /**
     * Speichert den aktuellen Log-Level. Zugriffe erfolgen über den getter und setter
     * @var int
     */
    private $loglevel=LL_ERROR;
    
    /**
     * Setter für den Loglevel
     * @param int $loglevel
     * @return \Sunhill\loggable
     */
    public function set_loglevel(int $loglevel) {
        $this->loglevel = $loglevel;
        return $this;
    }
    
    /**
     * Getter für den Loglevel
     * @return number
     */
    public function get_loglevel() {
        return $this->loglevel;
    }
    
    /**
     * Die Methoden trägt eine Debugmessage in das Log ein, sofern der Loglevel auf LL_DEBUG steht
     * @param string $message
     */
    protected function debug(string $message) {
        if (!$this->check_loglevel(LL_DEBUG)) {
            return;
        }
        Log::debug($message);
    }
    
    /**
     * Die Methode trägt eine Information in das Log ein, sofern der Loglevel mindestend auf LL_INFO steht
     * @param string $message
     */
    protected function info(string $message) {
        if (!$this->check_loglevel(LL_INFO)) {
            return;
        }
        Log::info($message);
    }
    
    /**
     * Trägt eine Anmerkung in das Log ein
     * @param string $message
     */
    protected function notice(string $message) {
        if (!$this->check_loglevel(LL_NOTICE)) {
            return;
        }
        Log::notice($message);
    }
    
    /**
     * Trägt eine Warnung in das Log ein
     * @param string $message
     */
    protected function warning(string $message) {
        if (!$this->check_loglevel(LL_WARNING)) {
            return;
        }
        Log::warning($message);
    }
    
    /**
     * Trägt einen Fehlernachricht in das Log ein
     * @param string $message
     */
    protected function error(string $message) {
        if (!$this->check_loglevel(LL_ERROR)) {
            return;
        }
        Log::error($message);
    }
    
    /**
     * Trägt eine kritische Nachricht in das Log ein
     * @param string $message
     */
    protected function critical(string $message) {
        if (!$this->check_loglevel(LL_CRITICAL)) {
            return;
        }
        Log::critical($message);
    }
    
    /**
     * Trägt eine Alarmnachricht in das Log ein
     * @param string $message
     */
    protected function alert(string $message) {
        if (!$this->check_loglevel(LL_ALERT)) {
            return;
        }
        Log::alert($message);
    }
    
    /**
     * Trägt eine Notfallnachricht in das Log ein
     * @param string $message
     */
    protected function emergency(string $message) {
        if (!$this->check_loglevel(LL_EMERGENCY)) {
            return;
        }
        Log::emergency($message);
    }
    
    /**
     * Prüft, ob der angeforderte Loglevel größer oder gleich dem momentan gesetzten ist, also
     * ob die Nachricht geloggt werden sollte
     * @param int $requested
     * @return boolean
     */
    private function check_loglevel(int $requested) {
        if ($requested >= $this->loglevel) {
            return true;
        } else {
            return false;
        }
    }
}