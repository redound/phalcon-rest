<?php

namespace OA\PhalconRest;

class Exception extends \Exception
{
	public function __construct($key, $message = null)
	{
		
		$this->code = $key;
		$this->message = $message;
	}
}
