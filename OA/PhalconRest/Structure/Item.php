<?php

namespace OA\PhalconRest\Structure;

use Phalcon\Mvc\Model\Relation as PhalconRelation;

class Item {

	protected $_data;
	protected $_collections = [];

	public function __construct($rootObject, $relations = [])
	{

		$this->_data = $rootObject;
		$this->_relations = $relations;
		$this->_createCollections($relations);
	}

	protected function _createCollections()
	{

		foreach ($this->_relations as $relation){

			$model = strtolower($relation->getReferencedModel());

			$this->_collections[$model] = [
				'type' => $relation->getType(),
				'data' => new Collection('id')
			];
		}
	}

	public function getPrimary()
	{

		return $this->_data->id;
	}

	public function addChild($object)
	{

		$model = $object['model'];
		$object = new Item($object['object']);

		if (!array_key_exists($model, $this->_collections)){
			return;
		}

		$this->_collections[$model]['data']->addObject($object);
	}

	protected function getRelated($value)
	{

		$related = $this->_collections[$value];

		if ($related['type'] == PhalconRelation::HAS_ONE){

			return $related['data']->getFirst();

		} elseif ($related['type'] == PhalconRelation::HAS_MANY){

			return $related['data']->getObjects();

		}
	}

	public function __get($value)
	{

		if (isset($this->_data->$value)){

			return $this->_data->$value;
		}

		if (isset($this->_collections[$value])){

			return $this->getRelated($value);
		}
	}
}
