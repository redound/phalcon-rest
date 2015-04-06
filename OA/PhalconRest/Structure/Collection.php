<?php

namespace OA\PhalconRest\Structure;

class Collection implements \IteratorAggregate {

	protected $_objects = [];
	protected $_primaryKey;
	protected $_relation;

	public function __construct($relation = null)
	{

		$this->_relation = $relation;
		return $this;
	}

	public function getRelation()
	{

		return $this->_relation;
	}

	public function getIterator()
	{

        return new CollectionIterator($this->_objects);
    }

	public function toArray()
	{

		return $this->_objects;
	}

	public function getObjects()
	{

		return array_values($this->_objects);
	}

	public function getFirst()
	{

		if (count($this->_objects)){

			$objects = array_values($this->_objects);
			return $objects[0];
		}
	}

	public function find($key, $value)
	{

		foreach ($this->_objects as $object){

			if ($object->$key == $value){

				return $object;
			}

		}

		return false;
	}

	public function addObject($object)
	{

		$primary = $object->getPrimary();

		if ( ! isset($this->_objects[$primary])){

			$this->_objects[$primary] = $object;
		}
	}

	public function addChild($id, $object)
	{

		$this->_objects[$id]->addChild($object);
	}
}
