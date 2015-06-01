<?php

namespace OA\PhalconRest\Mvc;

use OA\Phalcon\Validation\Validator,
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
		if ($this->_validator)
		{
			$message = $this->_validator->getFirstMessage();
		}

		if ($messages = $this->getMessages())
		{
			$message = $messages[0]->getMessage();
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
