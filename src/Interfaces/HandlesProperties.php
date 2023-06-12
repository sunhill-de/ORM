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
    public function handlePropertyArray($property);
    public function handlePropertyBoolean($property);
    public function handlePropertyCalculated($property);
    public function handlePropertyDate($property);
    public function handlePropertyDateTime($property);
    public function handlePropertyEnum($property);
    public function handlePropertyFloat($property);
    public function handlePropertyInteger($property);
    public function handlePropertyMap($property);
    public function handlePropertyObject($property);
    public function handlePropertyTags($property);
    public function handlePropertyText($property);
    public function handlePropertyTime($property);
    public function handlePropertyTimestamp($property);
    public function handlePropertyVarchar($property);
}

