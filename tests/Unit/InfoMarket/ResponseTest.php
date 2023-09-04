<?php
namespace Sunhill\ORM\Tests\Unit\Response;

use Sunhill\Basic\Tests\SunhillNoAppTestCase;
use Sunhill\ORM\InfoMarket\Response;
use Sunhill\ORM\InfoMarket\Exceptions\TypeNotFoundException;
use Sunhill\ORM\Tests\TestCase;

class ResponseTest extends TestCase
{
    
    protected function getElements(&$test)
    {
        return $this->getProtectedProperty($test,'elements');
    }
    
    protected function getElement(&$test,string $element)
    {
        return $this->getElements($test)->$element;
    }
    
    public function testInitializesEmpty()
    {
        $test = new Response();
        $this->assertTrue(empty((array)$this->getElements($test)));
    }
    
    public function testReturnAEmptyJson()
    {
        $test = new Response();
        $this->assertEquals('{',$test->get()[0]);
    }
    
    public function testAddEntry()
    {
        $test = new Response();
        $this->invokeMethod($test,'setElement',['some','value']);
        $this->assertEquals('value',$this->getElement($test,'some'));
    }
    
    public function testOK()
    {
        $test = new Response();
        $test->OK();
        $this->assertEquals('OK',$this->getElement($test,'result'));
    }
    
    public function testFailed()
    {
        $test = new Response();
        $test->Failed();
        $this->assertEquals('FAILED',$this->getElement($test,'result'));
    }
    
    public function testRequest()
    {
        $test = new Response();
        $test->Request('test.request');
        $this->assertEquals('test.request',$this->getElement($test,'request'));
    }
    
    /**
     * @dataProvider TypeProvider
     */
    public function testType($in_type,$out_type)
    {
        if ($out_type == 'except') {
            $this->expectException(TypeNotFoundException::class);
        }
        $test = new Response();
        $test->type($in_type);
        $this->assertEquals($out_type,$this->getElement($test,'type'));
    }
    
    public static function TypeProvider()
    {
        return [
            ['Array','array'],
            ['bool','bool'],
            ['Date','date'],
            ['Datetime','datetime'],
            ['Float','float'],
            ['Int','int'],
            ['object','object'],
            ['varchar','varchar'],
            ['Time','time'],
            ['unknown','except'],
            ['dAtE','date'],
            
        ];
    }
    
    /**
     * @dataProvider UnitProvider
     * @param unknown $unit
     * @param unknown $out_unit_int
     * @param unknown $out_unit
     * @throws \Exception
     */
    public function testUnit($unit,$unit_out)
    {
        $test = new Response();
        try {
            $test->unit($unit);
        } catch (\Exception $e) {
            if ($unit_out == 'except') {
                $this->assertTrue(true);
                return;
            }
            throw $e;
        }
        $this->assertEquals($unit_out,$this->getElement($test,'unit'));
    }
    
    public static function UnitProvider()
    {
        return [
            ['Centimeter','cm'],
            ['Degreecelsius','°C'],
            ['Lux','lx'],
            ['Meter','m'],
            ['None',''],
            ['Percent','%'],
            ['Second','s'],
            ['Torr','mmHg'],
            ['Unknown','except'],
        ];
    }
    
    /**
     * @dataProvider SemanticProvider
     * @param unknown $unit
     * @param unknown $out_unit_int
     * @param unknown $out_unit
     * @throws \Exception
     */
    public function testSemantic($semantic,$semantic_out)
    {
        $test = new Response();
        try {
            $test->semantic($semantic);
        } catch (\Exception $e) {
            if ($semantic_out == 'except') {
                $this->assertTrue(true);
                return;
            }
            throw $e;
        }
        $this->assertEquals($semantic_out,$this->getElement($test,'semantic'));
    }
    
    public static function SemanticProvider()
    {
        return [
            ['unknown','except'],
            ['Duration','Duration'],
            ['Name','Name'],            
            ['Illuminance','Illuminance'],
        ];
    }
    
    /**
     * @dataProvider ValueProvider
     */
    public function testValue($semantic, $value, $human_readable)
    {
        $test = new Response();
        $test->semantic($semantic);
        try {
            $test->value($value);
        } catch (\Exception $e) {
            if ($human_readable == 'except') {
                $this->assertTrue(true);
            }
            return;
            throw $e;
        }
        $this->assertEquals($value,$this->getElement($test,'value'));
        $this->assertEquals($human_readable,$this->getElement($test,'human_readable_value'));
    }
    
    public static function ValueProvider()
    {
        return [
            ['Name','abc','abc'],
            // Test durations
            ['Duration',1,'1 second'],
            ['Duration',45,'45 seconds'],
            ['Duration',61,'1 minute and 1 second',61],
            ['Duration',85,'1 minute and 25 seconds',85],
            ['Duration',121,'2 minutes and 1 second',121],
            ['Duration',145,'2 minutes and 25 seconds',145],
            ['Duration',60*60+60+35,'1 hour and 1 minute'],
            ['Duration',60*60+60*2+35,'1 hour and 2 minutes'],
            ['Duration',60*60*2+60+35,'2 hours and 1 minute'],
            ['Duration',60*60*2+60*2+35,'2 hours and 2 minutes'],
            ['Duration',60*60*24+60*60+35,'1 day and 1 hour'],
            ['Duration',60*60*24+2*60*60+35,'1 day and 2 hours'],
            ['Duration',60*60*24*2+60*60+35,'2 days and 1 hour'],
            ['Duration',60*60*24*2+60*60*2+35,'2 days and 2 hours'],
            ['Duration',60*60*24*365+60*60*24+60*60+35,'1 year and 1 day'],
            ['Duration',60*60*24*365+60*60*24*2+2*60*60+35,'1 year and 2 days'],
            ['Duration',60*60*24*365*2+60*60*24+60*60+35,'2 years and 1 day'],
            ['Duration',60*60*24*365*2+60*60*24*2+60*60*2+35,'2 years and 2 days'],
            // Test Capacity
            ['Capacity',1,'1 Byte'],
            ['Capacity',2,'2 Byte'],
            ['Capacity',1000,'1 kB'],
            ['Capacity',1001,'1 kB'],
            ['Capacity',1100,'1.1 kB'],
            ['Capacity',1000*1000,'1 MB'],
            ['Capacity',1000*1010,'1 MB'],
            ['Capacity',1000*1100,'1.1 MB'],
            ['Capacity',1000*1000*1000,'1 GB'],
            ['Capacity',1000*1010*1000,'1 GB'],
            ['Capacity',1000*1100*1000,'1.1 GB'],
            ['Capacity',1000*1000*1000*1000,'1 TB'],
            ['Capacity',1000*1010*1000*1000,'1 TB'],
            ['Capacity',1000*1100*1000*1000,'1.1 TB'], 
        ];
    }
    
    public function testError()
    {
        $test = new Response();
        $test->error('SOMEMESSAGE','SOMEERROR');
        
        $this->assertEquals('SOMEERROR',$this->getElement($test,'error_code'));
        $this->assertEquals('SOMEMESSAGE',$this->getElement($test,'error_message'));
        $this->assertEquals('FAILED',$this->getElement($test,'result'));
    }
    
    
    public function testErrorDefault()
    {
        $test = new Response();
        $test->error('SOMEMESSAGE');
        
        $this->assertEquals('UNKNOWNERROR',$this->getElement($test,'error_code'));
        $this->assertEquals('SOMEMESSAGE',$this->getElement($test,'error_message'));
        $this->assertEquals('FAILED',$this->getElement($test,'result'));
    }
    
}