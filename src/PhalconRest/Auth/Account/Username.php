<?php

namespace PhalconRest\Auth\Account;

use PhalconRest\Exceptions\UserException,
	PhalconRest\Models\Users,
	PhalconRest\Models\UsernameAccounts,
	PhalconRest\Constants\ErrorCodes as ErrorCodes;

class Username extends \Phalcon\Mvc\User\Plugin implements \PhalconRest\Auth\Account
{
	public function __construct($name, \Phalcon\Mvc\Model $userModel)
	{
		$this->name 		= $name;
		$this->userModel 	= get_class($userModel);
	}

	public function login($username = null, $password = null)
	{
		$userModel = $this->userModel;

		$usernameAccount = $userModel::getByUsername($username);

		// Check if password is valid
		if (!$usernameAccount || !$usernameAccount->validatePassword($password)) {
			return false;
		}

		// Something is terribly wrong, can't find the real user
		if (!$user = $usernameAccount->user) {
			return false;
		}

		if ($usernameAccount->user->active != 1){

			throw new UserException(ErrorCodes::USER_NOTACTIVE, 'User should be activated first');
		}

		return $user;
	}
}
