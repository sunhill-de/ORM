<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;
use Sunhill\ORM\Semantic\Age;
use Sunhill\ORM\Semantic\Duration;
use Sunhill\ORM\Semantic\Identifier;
use Sunhill\ORM\Semantic\IP4Address;
use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Semantic\PointInTime;
use Sunhill\ORM\Semantic\SemanticInSpace;
use Sunhill\ORM\Semantic\SemanticInTime;
use Sunhill\ORM\Units\Centimeter;
use Sunhill\ORM\Units\Kilometer;
use Sunhill\ORM\Units\Meter;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Units\Second;
use Sunhill\ORM\Semantic\Temperature;
use Sunhill\ORM\Units\Degreecelsius;
use Sunhill\ORM\Units\Lux;
use Sunhill\ORM\Units\Percent;
use Sunhill\ORM\Units\Torr;
use Sunhill\ORM\Semantic\Illuminance;
use Sunhill\ORM\Semantic\Capacity;
use Sunhill\ORM\Units\Byte;

class Market extends Marketeer
{
    
    protected $semantics = [];
    
    protected $units = [];
    
    public function installSemantic(string $name, string $semantic)
    {
        $this->semantics[$name] = $semantic;       
    }
    
    public function findSemantic(string $name)
    {
        if (isset($this->semantics[$name])) {
            return $this->semantics[$name];
        }
        return null;
    }
    
    public function installUnit(string $name, string $unit)
    {
        $this->units[$name] = $unit;    
    }
    
    public function findUnit(string $name)
    {
        if (isset($this->units[$name])) {
            return $this->units[$name];
        }
        return null;        
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->installDefaultSemantics();
        $this->installDefaultUnits();
    }

    protected function installDefaultSemantics()
    {
        $this->installSemantic('Age',Age::class);
        $this->installSemantic('Duration',Duration::class);
        $this->installSemantic('Identifier',Identifier::class);
        $this->installSemantic('IP4Address', IP4Address::class);
        $this->installSemantic('Name', Name::class);
        $this->installSemantic('PointInTime', PointInTime::class);
        $this->installSemantic('SemanticInSpace', SemanticInSpace::class);
        $this->installSemantic('SemanticInTime', SemanticInTime::class);        
        $this->installSemantic('Temperature', Temperature::class);
        $this->installSemantic('Illuminance', Illuminance::class);
        $this->installSemantic('Capacity', Capacity::class);
    }
    
    protected function installDefaultUnits()
    {
        $this->installUnit('Centimeter', Centimeter::class);
        $this->installUnit('Kilometer', Kilometer::class);
        $this->installUnit('Meter', Meter::class);
        $this->installUnit('None', None::class);
        $this->installUnit('Second', Second::class);
        $this->installUnit('Degreecelsius', Degreecelsius::class);
        $this->installUnit('Lux', Lux::class);
        $this->installUnit('Percent', Percent::class);
        $this->installUnit('Torr', Torr::class);
        $this->installUnit('Byte', Byte::class);
    }
    
    public function installMarketeer(string $name, string $marketeer)
    {
        $this->addEntry($name, $marketeer);  
    }
    
    /**
     * gets the value and metadata of the given item $path, checks the access rights and returns it in the wanted format
     * @param $path string: The dot separated path to the item (or branch)
     * @param $credentials string: The current credentials of the user
     * @param $format string ('json', 'object', 'array') The desired output format
     */
    public function getItem(string $path, string $credentials = 'anybody', string $format = 'json')
    {
        $path_elements = explode('.',$path);
        
        try {
            $item = $this->requestItem($path_elements);
        } catch (\Exception $e) {
            return $this->handleException($path, $format, $e);
        }
        
        if (is_null($item)) {
            return $this->itemNotFound($path);
        }
        return $this->processResponse($this->translateToResponse($item), $format);
    }

    protected function translateToResponse($item): \StdClass
    {
        $response = new \StdClass();
        $response->status = 'OK';
        $response->value  = $item->getValue();
        return $response;
    }
    
    protected function processResponse($response, string $format)
    {
        switch ($format) {
            case 'stdclass':
                return $response;
                break;
            case 'json':
                return json_encode($repsonse);
                break;
        }
    }
    
    protected function handleException(string $path, string $format, $exception)
    {
        switch ($exception::class) {
            case ItemNotReadableException::class:
                return $this->handleError('ITEMNOTREADABLE', "The item is not readable.");
                break;
        }
    }
    
    protected function itemNotFound(string $path, string $format)
    {
        
    }
    
    /**
     * gets the value and metadata of the given item $path, checks the access rights and sets the value
     * @param $path string: The dot separated path to the item (or branch)
     * @param $value : The value to set
     * @param $credentials string: The current credentials of the user
     * @param $format string ('json', 'object', 'array') The desired output format
     */
    public function setItem(string $path, $value, string $credentials = 'anybody', string $format = 'json')
    {
    }
        
}