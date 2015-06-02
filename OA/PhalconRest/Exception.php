<?php

namespace OA\PhalconRest;

class Exception extends \Exception
{
	public function __construct($key, $message = null)
	{		
		$this->key = $key;
		$this->message = $message;
	}

	public function getKey()
	{
		return $this->key;
	}
}
