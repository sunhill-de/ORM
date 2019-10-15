<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

/**
 * Basisklasse für die Sunhill-Tests
 * Die eigentlichen Tests sollten dann aber von sunhill_testcase_nodb oder sunhill_testcase_db abgeleitet werden
 * @author lokal
 *
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    static $db_up = false;
    
    /**
     * Holds an application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->app = $this->createApplication();
    }
    
    /**
     * Verhindert das too-many-connections problem bei meheren Datenbankrelevanten tests
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    public function tearDown() {
        DB::connection()->setPdo(null);
        parent::tearDown();
    }

    /**
     * Wrapper für Wertermittlung
     * Ist $fieldname nur ein einfacher string wird $loader->$fieldname zurückgegeben
     * Ist $fieldname in der Form irgendwas[index] wird $loader->$irgendwas[$index] zurückgegeben
     * Ist $fieldname in der Form irgendwas->subfeld wird $loader->$irgendwas->$subfeld zurückgegeben
     * Ist $fieldname in der Form irgendwas[index]->subfeld wird $loader->$irgendwas[$index]->$subfeld zurückgegeben
     * @param unknown $loader
     * @param unknown $fieldname
     * @return unknown
     */
    protected function get_field($loader,$fieldname) {
        $match = '';
        if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            $index = $match['index'];
            return $loader->$name[$index]->$subfield;
        } else if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]\[(?P<index2>\w+)\]/',$fieldname,$match)) {
            $name = $match['name'];
            $index2 = $match['index2'];
            $index = $match['index'];
            return $loader->$name[$index][$index2];
        } else if (preg_match('/(?P<name>\w+)->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            return $loader->$name->$subfield;
        } if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]/',$fieldname,$match)){
            $name = $match['name'];
            $index = $match['index'];
            return $loader->$name[$index];
        }  else {
            return $loader->$fieldname;
        }
    }
    
    
}
