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
use Sunhill\ORM\Semantic\Count;
use Sunhill\ORM\Semantic\Direction;
use Sunhill\ORM\Semantic\Height;
use Sunhill\ORM\Semantic\Pressure;
use Sunhill\ORM\Semantic\Speed;
use Sunhill\ORM\Units\Degree;
use Sunhill\ORM\Units\Kilometerperhour;
use Sunhill\ORM\Units\Meterpersecond;
use Sunhill\ORM\Units\Millimeter;
use Sunhill\ORM\Units\Pascal;
use Illuminate\Support\Facades\Cache;

class Market extends Marketeer
{
/**
 * **************** Cache management *************************
 */
    
 /*   
 * @var boolean indicates if the cache is enabled or not
 */    
    protected $cache_enabled = true;
    
    /**
     * Sets the value for cache_enabled
     * @param bool $value (default true)
     * @return \Sunhill\ORM\InfoMarket\Market (returns $this)
     */
    public function setCacheEnabled(bool $value = true)
    {
        $this->cache_enabled = $value;
        return $this;
    }
    
    /**
     * Gets the value for cache_enabled
     * @return bool
     */
    public function getCacheEnabled(): bool
    {
        return $this->cache_enabled;    
    }
    
// ***************************** Semantics ************************************    
    protected $semantics = [];
    
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
    
    public function getSemantics(): array
    {
        return $this->semantics;    
    }
    
// ********************************* Units ************************************    
    protected $units = [];
    
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
    
    public function getUnits()
    {
        return $this->units;    
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
        $this->installSemantic('Capacity', Capacity::class);
        $this->installSemantic('Count', Count::class);
        $this->installSemantic('Direction', Direction::class);
        $this->installSemantic('Duration',Duration::class);
        $this->installSemantic('Height', Height::class);
        $this->installSemantic('Identifier',Identifier::class);
        $this->installSemantic('Illuminance', Illuminance::class);
        $this->installSemantic('IP4Address', IP4Address::class);
        $this->installSemantic('Name', Name::class);
        $this->installSemantic('PointInTime', PointInTime::class);
        $this->installSemantic('Pressure', Pressure::class);
        $this->installSemantic('SemanticInSpace', SemanticInSpace::class);
        $this->installSemantic('SemanticInTime', SemanticInTime::class);        
        $this->installSemantic('Speed', Speed::class);
        $this->installSemantic('Status', Temperature::class);
        $this->installSemantic('Temperature', Temperature::class);
    }
    
    protected function installDefaultUnits()
    {
        $this->installUnit('Byte', Byte::class);
        $this->installUnit('Centimeter', Centimeter::class);
        $this->installUnit('Degree', Degree::class);
        $this->installUnit('Degreecelsius', Degreecelsius::class);
        $this->installUnit('Kilometer', Kilometer::class);
        $this->installUnit('Kilometerperhour', Kilometerperhour::class);
        $this->installUnit('Lux', Lux::class);
        $this->installUnit('Meter', Meter::class);
        $this->installUnit('Meterpersecond', Meterpersecond::class);
        $this->installUnit('Millimeter', Millimeter::class);
        $this->installUnit('None', None::class);
        $this->installUnit('Pascal', Pascal::class);
        $this->installUnit('Percent', Percent::class);
        $this->installUnit('Second', Second::class);
        $this->installUnit('Torr', Torr::class);
    }
    
    public function installMarketeer(string $name, string $marketeer)
    {
        $this->addEntry($name, $marketeer);  
    }

    protected function postprocessCacheValue($value, $format)
    {
        switch ($format) {
            case 'json':
                return $value;
                break;
            case 'stdclass':
            case 'object':
                return json_decode($value, false);
                break;
            case 'array':
                return json_decode($value, true);
                break;
        }
    }
    
    protected function searchCache($key, $format)
    {
        if (!$this->cache_enabled) {
            return;
        }
        
        if (Cache::has($key)) {
            return $this->postprocessCacheValue(Cache::get($key), $format);
        }
    }
    
    /**
     * If the cache is enabled than store this value to the cache
     * 
     * @param string $key The cache key
     * @param string $value The cache value 
     * @param int $ttl How many second should this entry be value (default 1 second = ASAP)
     */
    protected function updateCache(string $key, string $value, int $ttl=1, string $philosophy = 'single')
    {
        if (!$this->cache_enabled) {
            return;
        }
        switch ($philosophy) {
            case 'nocaching':
                return;
            default: 
                $expires = now()->addSeconds($ttl);
                Cache::add($key, $value, $expires);
        }
    }
        
    protected function getOfferCacheMiss(string $path, string $credentials, string $format)
    {
        $path_elements = empty($path)?[]:explode('.',$path);
        
        try {
            $offer = $this->requestOffer($path_elements);
        } catch (\Exception $e) {
            return $this->handleException($path, $format, $e);
        }
        
        $this->updateCache('offer:'.$path, json_encode($offer));
        
        return $this->processResponse($offer, $format);        
    }
    
    public function getOffer(string $path, string $credentials = 'anybody', string $format = 'json')
    {        
        if ($result = $this->searchCache('offer:'.$path, $format)) {
            return $result;
        }
        return $this->getOfferCacheMiss($path, $credentials, $format);
    }
    
    protected function requestGroup($path_elements)
    {
        if (empty($path_elements)) {
            return;            
        }
        
        do {
            $item = $this->requestItem($path_elements);
            $last = array_pop($path_elements);
        } while (!empty($path_elements) && ($item->getCachePhilosophy() !== 'group'));
        
        if ($item->getCachePhilosophy() == 'group') {
            array_push($path_elements, $last);
            return ['item'=>$item, 'path'=>implode('.', $path_elements)];
        } else {
            return;
        }
    }
    
    protected function getUpdate($update)
    {
        return 1;    
    }
    
    protected function handleCacheGroup($item)
    {
        if (!($offering = $item['item']->requestOffer([]))) {
            return;
        }
        foreach ($offering as $offer) {
            $subitem = $item['item']->getProperty($offer);
            $this->handleCacheGroup(['item'=>$subitem,'path'=>$item['path'].'.'.$subitem->getName()]);
            $response = $this->translateToResponse($subitem);
            $ttl = $this->getUpdate($response->get('stdclass')->update);
            $this->updateCache($item['path'].'.'.$subitem->getName(), $response->get('json'), $ttl);
        }
    }
    
    protected function searchItemOrCacheGroup($path_elements)
    {
        if ($item_info = $this->requestGroup($path_elements)) {
            $this->handleCacheGroup($item_info, implode('.',$path_elements)); 
            if ($item = $this->searchCache(implode('.',$path_elements),'stdclass')) {
                return $item;
            }
        }
        $item = $this->requestItem($path_elements);
        return $item;
    }
    
    protected function getItemCacheMiss(string $path, string $credentials, string $format)
    {
        $path_elements = empty($path)?[]:explode('.',$path);
        
        try {
            $item = $this->searchItemOrCacheGroup($path_elements);
        } catch (\Exception $e) {
            return $this->handleException($path, $format, $e);
        }
        
        if (($item === false) || is_null($item)) {
            return $this->itemNotFound($path, $format);
        }
        $response = $this->translateToResponse($item);
        $response->request( $path );
        $response->setElement('credentials', $credentials);
   
        $this->updateCache($path, $response->get('json'));
        
        return $this->processResponse($response, $format);        
    }
    
    /**
     * gets the value and metadata of the given item $path, checks the access rights and returns it in the wanted format
     * @param $path string: The dot separated path to the item (or branch)
     * @param $credentials string: The current credentials of the user
     * @param $format string ('json', 'object', 'array') The desired output format
     */
    public function getItem(string $path, string $credentials = 'anybody', string $format = 'json')
    {
        if ($result = $this->searchCache($path, $format)) {
            return $result;
        }
        
        return $this->getItemCacheMiss($path, $credentials, $format);
    }

    protected function translateToResponse($item): Response
    {
        if (is_a($item, Response::class)) {
                    return $item;
        } else if (is_a($item, Marketeer::class)) {
                    $response = new Response();
                    $response->OK()
                    ->unit('none')
                    ->semantic('Name')
                    ->value($item->getAllProperties())
                    ->readable(true)
                    ->writeable(false)
                    ->type($item::getType());
                    
                    return $response;
        } else if (is_a($item, \Stdclass::class)) {
                    $response = new Response();
                    $response->OK()
                    ->unit($item->unit)
                    ->semantic($item->semantic)
                    ->value($item->value)
                    ->readable($item->readable)
                    ->writeable($item->writeable)
                    ->type($item->type);
                    return $response;
        } else if (is_a($item, Property::class)) {
                    $response = new Response();
                    $response->OK()
                    ->unit($item->getUnit())
                    ->semantic($item->getSemantic())
                    ->value($item->getValue())
                    ->readable($item->isReadable())
                    ->writeable($item->isWriteable())
                    ->type($item::getType());
                    return $response;
         }
    }
    
    protected function processResponse($response, string $format)
    {
        if (is_array($response)) {
            switch ($format) {
                    case 'json':
                        return json_encode($response);
                        break;
                    case 'stdclass':
                    case 'object':
                        return json_decode(json_encode($response), false);
                        break;
                    case 'array':
                        return $response;
                        break;
             }            
        } else {
            return $response->get($format);
        }
    }
    
    protected function handleException(string $path, string $format, $exception)
    {
        switch ($exception::class) {
            case ItemNotReadableException::class:
                return $this->handleError('ITEMNOTREADABLE', "The item is not readable.", $format);
                break;
        }
    }
    
    protected function itemNotFound(string $path, string $format)
    {
        return $this->handleError('ITEMNOTFOUND', "The item '$path' was not found.", $format);
    }
    
    protected function handleError($error_code, $error_message, $format)
    {
        $response = new Response();
        $response->failed()
            ->error($error_message,$error_code);
        return $this->processResponse($response, $format);
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
        $path_elements = empty($path)?[]:explode('.',$path);
        
        try {
            $item = $this->requestItem($path_elements);
        } catch (\Exception $e) {
            return $this->handleException($path, $format, $e);
        }
        
        if (($item === false) || is_null($item)) {
            return $this->itemNotFound($path, $format);
        }
        
        $item->setValue($value);
       
        $response = $this->translateToResponse($item);
        $response->request( $path );
        $response->setElement('credentials', $credentials);
        
        return $this->processResponse($response, $format);
    }
    
    public function lookupValueInCache(string $path, string $type = 'unknown', string $credentials = 'anybody')
    {
        
    }
    
    /**
     * Inserts a value in the market cache. 
     * @param string $path The ID of the item that should be inserted
     * @param string $type The name of the type. If set to unknown the method looks it up
     * @param bool $track Is set to true, the previous value is saved to allow a history
     * @param string $credentials The credentials of the current user
     */
    public function insertValueInCache(string $path, string $type = 'unknown', bool $track = false, string $credentials = 'anybody')
    {
        
    }
    
}