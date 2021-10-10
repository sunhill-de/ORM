<?php

/**
 *
 * @file objectlist
 * Implements the class objectlist
 * lang=en
 * Reviewstatus: 2020-08-10
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 */
namespace Sunhill\ORM\Utils;

use Sunhill\ORM\ORMException;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;

class ObjectListException extends ORMException
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
            $this->items[$index] = Objects::load($this->items[$index]);
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

    public function getClass(int $index)
    {
        if (! isset($this->class_cache[$index])) {
            $this->class_cache[$index] = Classes::normalizeNamespace(Objects::getClassNamespaceOf($this->get_id($index)));
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
            $class = $this->getClass($i);
            if (! in_array($class, $result)) {
                $result[] = $class;
            }
        }
        return $result;
    }

    /**
     * It removes all objects from the list that are not a $class or (depending on $children) a child of $class
     * @param string $class is either the name of the class or its namespace
     * @param bool $children if this parameter is true, also all children of $class a kept otherwise removed too
     */
    public function filter_class(string $class, bool $children = true)
    {
        $class = Classes::getNamespaceOfClass($class);
        $shadow_items = [];
        $shadow_classes = [];
        for ($i = 0; $i < count($this->items); $i ++) {
            if ($children) {
                if (is_a($this->get($i), $class)) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->getClass($i);
                }
            } else {
                if ($this->getClass($i) === $class) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->class_cache[$i];
                }
            }
        }
        $this->items = $shadow_items;
        $this->class_cache = $shadow_classes;
    }

    /**
     * It removes all objects from the list that are a $class or (depending on $children) a child of $class
     * @param string $class is either the name of the class or its namespace
     * @param bool $children if this parameter is true, also all children of $class a removed otherwise kept
     */
    public function remove_class(string $class, bool $children = true)
    {
        $class = Classes::getNamespaceOfClass(Classes::searchClass($class));
        $shadow_items = [];
        $shadow_classes = [];
        for ($i = 0; $i < count($this->items); $i ++) {
            if ($children) {
                if (! is_a($this->get($i), $class)) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->getClass($i);
                }
            } else {
                if ($this->getClass($i) !== $class) {
                    $shadow_items[] = $this->items[$i];
                    $shadow_classes[] = $this->getClass($i);
                }
            }
        }
        $this->items = $shadow_items;
        $this->class_cache = $shadow_classes;
    }
}