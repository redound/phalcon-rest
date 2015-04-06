<?php

namespace OA\PhalconRest\Middleware;

class Authentication extends \Phalcon\Mvc\User\Plugin {

	public function beforeExecuteRoute()
	{

		$jwt_token = $this->request->getToken();
		$decoded_jwt_token 	= null;

		try {

			$decoded_jwt_token = \JWT::decode($jwt_token, JWT_SECRET, false);
		} catch (\UnexpectedValueException $e) {

			// Token not valid
		}

		// Register user
		if ($decoded_jwt_token && $decoded_jwt_token->exp > (time() * 1000)) {
			$this->authservice->setUser($decoded_jwt_token->sub);
		}
	}
}
