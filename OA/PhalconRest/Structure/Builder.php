<?php

namespace OA\PhalconRest\Structure;

class Builder {

	public function setModel($model)
	{

		$this->model = $model;
		return $this;
	}

	public function getModel($model)
	{

		return $this->model;
	}

	public function setRelations($relations)
	{

		$this->relations = $relations;
	}

	public function getRelations()
	{

		return $this->relations;
	}

	protected function extractObject($model, $object)
	{

		$modelData = new \stdClass;
		$modelInstance = new $model;
		$columns = array_values($modelInstance->columnMap());

		$empty = true;
		foreach($columns as $column){
			$property = strtolower($model) . '_' . $column;
			$value = $object->$property;
			$modelData->$column = $value;
			if ($empty && !is_null($value)){
				$empty = false;
			}
		}

		if ($empty){

			return false;
		}

		return $modelData;
	}

	public function extractRelated($result)
	{

		$related = [];
		foreach($this->relations as $relation){
			$model = strtolower($relation->getReferencedModel());
			$object = $this->extractObject($model, $result);
			if ($object){
				$related[] = [
					'model' => $model,
					'object' => $object
				];
			}
		}
		return $related;
	}

	public function build($results)
	{

		$mainCollection = new Collection('id');

		foreach ($results as $result){

			$rootObject = $this->extractObject($this->model, $result);

			$rootObject = new Item($rootObject, $this->relations);

			$mainCollection->addObject($rootObject);

			$relatedObjects = $this->extractRelated($result);

			foreach($relatedObjects as $object){
				$mainCollection->addChild($rootObject->getPrimary(), $object);
			}

		}

		return $mainCollection;
	}
}
