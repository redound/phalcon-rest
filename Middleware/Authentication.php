<?php

namespace PhalconRest\Middleware;

class Authentication extends \Phalcon\Mvc\User\Plugin {

	public function beforeExecuteRoute()
	{

		$jwt_token = $this->request->getToken();
		$decoded_jwt_token 	= null;

		try {

			$decoded_jwt_token = \JWT::decode($jwt_token, $this->config->phalconRest->jwtSecret, ['HS256']);
		} catch (\UnexpectedValueException $e) {

			// Token not valid
		}

		// Register user
		if ($decoded_jwt_token && $decoded_jwt_token->exp > time()) {
			$this->authservice->setUser($decoded_jwt_token->sub);
		}
	}
}
