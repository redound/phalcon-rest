<?php

namespace PhalconRest\Services;

use PhalconRest\Exceptions\UserException,
	PhalconRest\Models\Users,
	PhalconRest\Models\UsernameAccounts,
	Library\PhalconRest\Constants\ErrorCodes as ErrorCodes;

class AuthUsername extends \Phalcon\Mvc\User\Plugin {

	protected function validPassword($password, $password2)
	{
		return $this->security->checkHash($password, $password2);
	}

	public function login($username, $password)
	{
		$usernameAccount = UsernameAccounts::findFirstByUsername($username);

		// Check if any user matches the username
		if (!$usernameAccount) {
			return false;
		}

		// Check if password is valid
		if (!$this->validPassword($password, $usernameAccount->password)) {
			return false;
		}

		$user = Users::findFirst($usernameAccount->userId);

		if ($user->active != 1){

			throw new UserException(ErrorCodes::USER_NOTACTIVE, 'User should be activated first');
		}

		// Something is terribly wrong, can't find the real user
		if (!$user) {
			return false;
		}

		return $user;
	}
}
