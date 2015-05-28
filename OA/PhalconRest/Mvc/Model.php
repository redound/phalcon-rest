<?php

namespace OA\PhalconRest\Mvc;

use OA\Phalcon\Validation\Validator,
	OA\PhalconRest\Structure\Builder as StructureBuilder,
	OA\PhalconRest\CoreException,
	OA\PhalconRest\UserException,
	OA\PhalconRest\Services\ErrorService as ERR;

class Model extends \Phalcon\Mvc\Model
{
	protected $_validator;

	public function beforeValidationOnCreate()
	{
		$this->createdAt = date('Y-m-d H:i:s');
		$this->updatedAt = date('Y-m-d H:i:s');
	}

	public function beforeValidationOnUpdate()
	{
		$this->updatedAt = date('Y-m-d H:i:s');
	}

	public function getValidator()
	{
		if (!$this->_validator){


			$this->_validator = Validator::make($this, $this->validateRules());
		}

		return $this->_validator;
	}

	public function validation()
	{
		if (!method_exists($this, 'validateRules')){
			return true;
		}

		$this->_validator = $this->getValidator();

		return $this->_validator->passes();
	}

	public function onValidationFails()
	{
		$message = null;
		if ($this->_validator){
			$message = $this->_validator->getFirstMessage();
		}

		if (is_null($message)){

			$message = 'Could not validate data';
		}

		throw new UserException(ERR::DATA_INVALID, $message);
	}

	public function prepare($data)
	{

		$whitelist = $this->whitelist();

		foreach ($whitelist as $field){
			if (isset($data->$field)) {
				$this->$field = $data->$field;
			}
		}
	}

	public static function genColumns($modelName, $prefix = true)
	{
		$prefix = ($prefix ? strtolower($modelName) . '_' : '');
		$cols = [];
		$model = new $modelName;
		$columns = $model->columnMap();

		foreach($columns as $column) {
			$cols[] = $modelName . '.' . $column . ' as ' . $prefix . $column;
		}

		return $cols;
	}

	public static function all($options = null, $single = null)
	{

		$modelName = get_called_class();
		$di = \Phalcon\DI::getDefault();
		$modelsManager = $di->getModelsManager();
		$modelRelations = $modelsManager->getHasOneAndHasMany(new $modelName);

		$columns = [];
		$models = [];
		$modelRelsByName = [];
		$wheres = [];

		$columns = self::genColumns($modelName);

		foreach($modelRelations as $relation) {
			$relModelName = $relation->getReferencedModel();
			$models[] = $relModelName;
			$modelRelsByName[$relModelName] = $relation;
			$columns = array_merge($columns, self::genColumns($relModelName));
		}

		$builder = $modelsManager->createBuilder();
		$builder->columns($columns);
		$builder->from($modelName);
		foreach($models as $model) {

			$opts = $modelRelsByName[$model]->getOptions();

			if (isset($opts['join'])) {
				$join = $opts['join'];
				$builder->$join($model);
			}
			else {

				$builder->leftJoin($model);
			}
		}

		// Add options
		if (!is_null($options)){
			foreach ($options as $key => $option) {

				switch ($key) {

					case 'limit':
					case 'orderBy':
						$builder->$key($option);
						break;

					default:
						$builder->$key($option[0], $option[1]);
						break;

				}
			}
		}

		if ($single) {
			$builder->limit(1);
		}

		$results = $builder->getQuery()->execute();

		$structureBuilder = new StructureBuilder;
		$structureBuilder->setModel($modelName);
		$structureBuilder->setRelations($modelRelations);
		$results = $structureBuilder->build($results);

		if ($single) {

			foreach($results as $result) {
				return $result;
			}

			return false;
		}

		return $results;
	}

	public static function findFullFirst($options = null)
	{
		return self::all($options, true);
	}

	public static function createFrom($data)
	{
		$modelName = get_called_class();
		$modelInstance = new $modelName;

		if (!isset($modelInstance->_whitelist)) {
			throw new CoreException('No whitelist declared for: ' . $modelName);
		}

	}

	public static function exists($id)
	{
		return self::findFirstById($id);
	}

	public static function remove($opts)
	{
		$result = self::findFirst($opts);

		if (!$result) {
			return false;
		}

		return $result->delete();
	}
}
