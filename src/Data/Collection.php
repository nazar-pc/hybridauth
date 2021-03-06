<?php
/*!
* HybridAuth
* http://hybridauth.github.io | http://github.com/hybridauth/hybridauth
* (c) 2015 HybridAuth authors | http://hybridauth.github.io/license.html
*/

namespace Hybridauth\Data;

/**
 * A very basic Data collection.
 */
final class Collection
{
    /**
    * Data collection
    *
    * @var mixed
    */
    protected $collection = null;

    /**
    * @param mixed $data
    */
    public function __construct($data = null)
    {
        $this->collection = new \stdClass();

        if (is_object($data)) {
            $this->collection = $data;
        }

        if (is_array($data)) {
            $this->collection = (object) $data;
        }
    }

    /**
    * Retrieves an item
    *
    * @param $property
    *
    * @return mixed
    */
    public function get($property)
    {
        if ($this->exists($property)) {
            return $this->collection->$property;
        }
    }

    /**
    * Add or update an item
    *
    * @param $property
    * @param mixed $value
    */
    public function set($property, $value)
    {
        if ($property) {
            $this->collection->$property = $value;
        }
    }

    /**
    * Returns the whole collection
    *
    * @return Collection
    */
    public function all()
    {
        return new Collection($this->collection);
    }

    /**
    * .. until I come with a better name..
    *
    * @param $property
    *
    * @return Collection
    */
    public function filter($property)
    {
        if ($this->exists($property)) {
            $data = $this->get($property);

            if (! is_a($data, 'Collection')) {
                $data = new Collection($data);
            }

            return $data;
        }

        return $this;
    }

    /**
    * Checks whether an item within the collection
    *
    * @param $property
    *
    * @return bool
    */
    public function exists($property)
    {
        return property_exists($this->collection, $property);
    }

    /**
    * Finds whether the collection is empty
    *
    * @return bool
    */
    public function isEmpty()
    {
        return ! (bool) $this->count();
    }

    /**
    * Count all items in collection
    *
    * @return int
    */
    public function count()
    {
        return count($this->properties());
    }

    /**
    * Returns all items properties names
    *
    * @return array
    */
    public function properties()
    {
        $properties = [];

        foreach ($this->collection as $property) {
            $properties[] = $property;
        }

        return $properties;
    }

    /**
    * Returns all items values
    *
    * @return array
    */
    public function values()
    {
        $values = [];

        foreach ($this->collection as $property) {
            $values[] = $this->get($property);
        }

        return $values;
    }
}
