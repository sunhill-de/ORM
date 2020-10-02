<?php

/**
 @file objectlist
 Implements the class objectlist
 lang=en
 */
namespace Sunhill\ORM\Utils;

use Sunhill\ORM\SunhillException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\oo_object;

class ObjectListException extends SunhillException
{
}

/**
 * A class that handles list of objects and provides some additional features
 *
 * @author Klaus
 *        
 */
class objectlist implements \countable, \ArrayAccess, \Iterator
{

    /**
     * Stores the objects
     */
    private $items = [];

    /**
     * Internal pointer for foreach
     */
    private $pointer = 0;

    /**
     * Caches the known classes of the objects
     */
    private $class_cache = [];

    /**
     * Adds a new entry
     *
     * @param $item is
     *            either an int or an object
     * @param $index default
     *            to null wich means append it to the array, otherwise replace the index
     */
    public function add($item, $index = null)
    {
        if (is_null($index)) {
            $this->items[] = $item;
        } else {
            $this->items[$index] = $item;
        }
    }

    public function empty()
    {
        return empty($this->items);
    }

    public function get_id(int $index)
    {
        if (! $this->is_valid($index)) {
            throw new ObjectListException("Invalid index '$index'");
        }
        if (is_int($this->items[$index])) {
            return $this->items[$index];
        } else {
            return $this->items[$index]->get_id();
        }
    }

    /**
     * Gets the item with the index $index.
     * If it's not already loaded, it loads it
     */
    public function get(int $index)
    {
        if (! $this->is_valid($index)) {
            throw new ObjectListException("Invalid index '$index'");
        }
        if (is_int($this->items[$index])) {
            $this->items[$index] = oo_object::load_object_of($this->items[$index]);
        }
        return $this->items[$index];
    }

    /**
     * Returns the count of items in this list
     */
    public function count()
    {
        return count($this->items);
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->add($value, $offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function current()
    {
        return $this->get($this->pointer);
    }

    public function key()
    {
        return $this->pointer;
    }

    public function next()
    {
        $this->pointer ++;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function valid(): bool
    {
        return $this->is_valid($this->pointer);
    }

    protected function is_valid(int $index)
    {
        return (($index >= 0) && ($index < count($this->items)));
    }

    public function get_class(int $index)
    {
        if (! isset($this->class_cache[$index])) {
            $this->class_cache[$index] = Classes::normalize_namespace(oo_object::get_class_name_of($this->get_id($index)));
        }
        return $this->class_cache[$index];
    }

    /**
     * Returns the distinct classes that are in this array right now
     */
    public function get_distinct_classes()
    {
        $result = [];
        for ($i = 0; $i < count($this->items); $i ++) {
            $class = $this->get_class($i);
            if (! in_array($class, $result)) {
                $result[] = $class;
            }
        }
        return $result;
    }

    public function filter_class(string $class, bool $children = true)
    {
        $shadow_items = [];
        $shadow_classes = [];
        for ($i = 0; $i < count($this->items); $i ++) {
            if ($children) {
                if (is_a($this->get($i), $class)) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->get_class($i);
                }
            } else {
                if ($this->get_class($i) === $class) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->class_cache[$i];
                }
            }
        }
        $this->items = $shadow_items;
        $this->class_cache = $shadow_classes;
    }

    public function remove_class(string $class, bool $children = true)
    {
        $shadow_items = [];
        $shadow_classes = [];
        for ($i = 0; $i < count($this->items); $i ++) {
            if ($children) {
                if (! is_a($this->get($i), $class)) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->get_class($i);
                }
            } else {
                if ($this->get_class($i) !== $class) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->get_class($i);
                }
            }
        }
        $this->items = $shadow_items;
        $this->class_cache = $shadow_classes;
    }
}