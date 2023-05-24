<?php
namespace Sunhill\ORM\Interfaces;

use Sunhill\ORM\Properties\Property;

/**
 * The interface HandlesProperties defines a handler for all currently defined property types.
 * If later a new property is added, the according handler has to be added to this interface.
 * Derrived classes throw an exception if they don't define the property handler.
 * 
 * @author klaus
 *        
 */
interface HandlesProperties
{
    public function handlePropertyArray(Property $property);
    public function handlePropertyBoolean(Property $property);
    public function handlePropertyCalculated(Property $property);
    public function handlePropertyDate(Property $property);
    public function handlePropertyDateTime(Property $property);
    public function handlePropertyEnum(Property $property);
    public function handlePropertyFloat(Property $property);
    public function handlePropertyInteger(Property $property);
    public function handlePropertyMap(Property $property);
    public function handlePropertyObject(Property $property);
    public function handlePropertyTags(Property $property);
    public function handlePropertyText(Property $property);
    public function handlePropertyTime(Property $property);
    public function handlePropertyTimestamp(Property $property);
    public function handlePropertyVarchar(Property $property);
}

