<?php

namespace Sunhill\ORM\InfoMarket;

use \StdClass;
use Sunhill\ORM\InfoMarket\Exceptions\TypeNotFoundException;
use Sunhill\ORM\InfoMarket\Exceptions\UnitNotFoundException;
use Sunhill\ORM\InfoMarket\Exceptions\FrequencyNotFoundException;
use Sunhill\ORM\InfoMarket\Exceptions\SemanticNotFoundException;
use Sunhill\ORM\Facades\InfoMarket;

class Response
{
    
    const DEFINED_TYPES = ['int','float','bool','varchar','text','date','time','datetime','array','object'];    
    
    const DEFINES_UPDATES = ['asap','second','minute','hour','day','late'];
    
    protected $type;
    
    protected $unit;
    
    protected $semantic;
    
    protected $elements;
    
    public function __construct()
    {
        $this->elements = new StdClass();
    }
    
    public function setElement(string $name,$value)
    {
        $this->elements->$name = $value;
        return $this;
    }
    
    public function getElement(string $name)
    {
        if (property_exists($this->elements,$name)) {
            return $this->elements->$name;
        } else {
            return "";
        }
    }
    
    /**
     * Checks if a update field was given. If not assume asap
     */
    protected function checkUpdate()
    {
        if (!$this->hasElement('update')) {
            $this->setElement('update','asap');
        }
    }
    
    /**
     * Returns the json response
     * @return string The response as a json string
     */
    public function get(string $answer = 'json')
    {
        $this->checkUpdate();
        switch ($answer) {
            case 'json':
                return json_encode($this->elements);
                break;
            case 'raw':
                return $this->elements->value;
                break;
            case 'object':
                return $this->elements;
                break;
            case 'array':
                return json_decode(json_encode($this->elements),true);
                break;
        }
        return json_encode($this->elements);
    }
    
    /**
     * Returns the response as a StdClass
     * The response as a StdClass
     */
    public function getStdClass(): StdClass
    {
        $this->checkUpdate();
        return $this->elements;
    }
    
    /**
     * Returns if a specific element is defined in the response. Normally only for
     * debugging and testing purposed
     * @param string $element
     * @return bool
     */
    public function hasElement(string $element): bool
    {
        return property_exists($this->elements,$element);
    }
    
    /**
     * Inidcates that the request was successful
     * @return Response
     */
    public function OK(): Response
    {
        $this->setElement('result','OK');
        return $this;
    }
    
    /**
     * Indicates that the request failed
     * @return Response
     */
    public function failed(): Response
    {
        $this->setElement('result','FAILED');
        return $this;
    }
    
    public function error(string $message,string $code='UNKNOWNERROR'): Response
    {
        return $this->failed()->errorCode($code)->errorMessage($message);
    }
    
    /**
     * Sets the request string
     * @param string $request
     * @return Response
     */
    public function request(string $request): Response
    {
        $this->setElement('request',$request);
        return $this;
    }
    
    /**
     * Sets the type
     * Checks if the type exists
     * @param string $type
     * @param unknown $subtype
     * @throws InfoMarketException
     * @return Response
     */
    public function type(string $type): Response
    {
        $type = strtolower($type);
        if (!in_array($type, STATIC::DEFINED_TYPES)) {
            throw new TypeNotFoundException("Unknown type '$type'.");            
        }
        $this->setElement('type', $type);
        return $this;
    }
    
    /**
     * Sets the unit and unit_int field according to the given (internal) unit
     * @param string $unit
     * @throws InfoMarketException
     * @return Response
     */
    public function unit(string $unit): Response
    {
        $unit = ucfirst(strtolower($unit));
        if (empty($unit) || ($unit == ' ')) {
            $unit = 'None';
        }
        if (!($namespace = InfoMarket::findUnit($unit))) {
            throw new UnitNotFoundException("Unknown type '$unit'.");            
        }
        $this->unit = new $namespace();
        $this->setElement('unit', $this->unit->getUnit());
        $this->setElement('unit_name', $unit);
        return $this;
    }
    
    public function update(string $key)
    {
        $type = strtolower($key);
        if (!in_array($key, STATIC::DEFINES_UPDATES)) {
            throw new FrequencyNotFoundException("Unkown update frequency '$key'.");            
        }
        $this->setElement('update',$key);
        return $this;
    }
    
    /**
     * Sets the semantic and semantic_int field according to the given (internal) semantic vaoue
     * @param string $unit
     * @throws InfoMarketException
     * @return Response
     */
    public function semantic(string $semantic): Response
    {
        $semantic = ucfirst(strtolower($semantic));
        if (!($namespace = InfoMarket::findSemantic($semantic))) {
            throw new SemanticNotFoundException("Unknown semantic '$semantic'.");            
        }
        $this->semantic = new $namespace();
        $this->setElement('semantic', $this->semantic->getName());
        if (is_null($this->unit)) {
            $this->unit($this->semantic->getUnit());
        }
        return $this;
    }
    
    /**
     * Tries to translate the given text in the current language. If no translation is found
     * the original is returned
     * @param string $text
     * @return \Sunhill\InfoMarket\Elements\Response\string
     */
    protected function translate(string $text)
    {
        return $text;
    }
    
    /**
     * Sets the value and at the same time the human_readable_value depending on unit which
     * has to be set before.
     * @param unknown $value
     * @throws InfoMarketException
     * @return Response
     */
    public function value($value): Response
    {
        if (isset($this->semantic)) {
            $value = $this->semantic::processValue($value);
        }
        $this->setElement('value',$value);
        
        if (isset($this->unit)) {
            $human_readable_unit = $this->unit->getUnit();
            $this->setElement('human_readable_unit',$human_readable_unit);
        }
        if (isset($this->semantic)) {
            $human_readable_value = $this->semantic->processHumanReadableValue($value,$human_readable_unit);
            $this->setElement('human_readable_value',$human_readable_value);
        }
        
        return $this;
    }
    
    public function errorCode(string $code): Response
    {
        $this->setElement('error_code',$code);
        return $this;
    }
    
    public function errorMessage(string $message): Response
    {
        $this->setElement('error_message',$this->translate($message));
        return $this;
    }
    
    public function infoNotFound(): Response
    {
        return $this->error('The information was not found.','INFONOTFOUND');
    }
    
    public function readable(bool $value = true)
    {
        $this->setElement('readable',$value);
    }
    
    public function writeable(bool $value = true)
    {
        $this->setElement('writeable',$value);
    }
}
