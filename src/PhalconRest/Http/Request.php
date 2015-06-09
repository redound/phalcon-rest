<?php

namespace PhalconRest\Http;

class Request extends \Phalcon\Http\Request
{

	protected function removeBearer($string)
	{
		return preg_replace('/.*\s/', '', $string);
	}

	public function getAuth($index)
	{

		$authHeader = $this->getHeader('AUTHORIZATION');
		$authString = $this->removeBearer($authHeader);

		$auth 		= base64_decode($authString);
		$authEx 	= explode(':', $auth);

		if (count($authEx) != 2) {

			return;
		}

		switch ($index) {

			case 'username':
				return $authEx[0];

			case 'password':
				return $authEx[1];

			default:
				return [
					'username' => $authEx[0],
					'password' => $authEx[1]
				];
		}
	}

	public function getUsername()
	{
		return $this->getAuth('username');
	}

	public function getPassword()
	{
		return $this->getAuth('password');
	}

	public function getToken()
	{
		$authHeader 		= $this->getHeader('AUTHORIZATION');
		$authQuery  		= $this->getQuery('token');

		return ($authQuery ? $authQuery : $this->removeBearer($authHeader));
	}
}
