<?php

namespace OA\PhalconRest\Services;

use OA\PhalconRest\UserException,
	OA\PhalconRest\CoreException,
	OA\PhalconRest\Constants\AccountTypes,
	OA\PhalconRest\Services\ErrorService as ERR;

class AuthGoogle extends \Phalcon\Mvc\User\Plugin
{
	protected $client;
	protected $_accessToken;

	public function __construct(\Google_Client $googleClient = null)
	{

		$this->client = $googleClient;
		$this->client->setClientId(GOOGLE_CLIENTID);
		$this->client->setClientSecret(GOOGLE_CLIENTSECRET);
		$this->client->setRedirectUri(GOOGLE_REDIRECTURI);
		$this->client->setScopes(GOOGLE_SCOPES);
	}

	public function codeValid($code)
	{

		if (isset($code)){

			try {
				$this->client->authenticate($code);
			} catch (\Exception $e) {
				throw new UserException(ERR::AUTH_BADLOGIN, 'Expired Google login');
			}

			$accessToken = $this->getAccessToken();
			$this->setAccessToken($accessToken);
			return true;

		}

		return false;
	}

	public function getAccessToken()
	{

		return $this->client->getAccessToken();
	}

	public function setAccessToken($token)
	{

		$this->client->setAccessToken($token);
	}

	public function getPayload()
	{

		$payload = $this->client->verifyIdToken()->getAttributes()['payload'];
		return $payload;
	}

	public function getUserInfo()
	{

		try {

			$plus = new \Google_Service_Plus($this->client);

			return $plus->people->get('me');

		} catch (\Exception $e) {

			throw new CoreException(ERR::GOOGLE_NODATA, 'Could not get user data');
		}

	}

	protected function register($payload, $userinfo)
	{

		$user = new \Users();
		$user->name             = $userinfo['displayName'];
		$user->email            = $payload['email'];
		$user->addAccountType(AccountTypes::GOOGLE);
		$user->active           = 1;
		$user->mailToken        = null;


		if (!$user->save()) {
		    throw new CoreException(ERR::USER_REGISTERFAIL, 'User could not be created');
		}

		$googleAccount             	= new \GoogleAccounts();
		$googleAccount->userId   	= $user->id;
		$googleAccount->googleId   	= $userinfo['id'];
		$googleAccount->email      	= $payload['email'];

		if (!$googleAccount->save()){

		    throw new CoreException(ERR::USER_REGISTERFAIL, 'GoogleAccount for user #' . $user->id . ' could not be created');
		}

		return $user;
	}

	public function login($code)
	{

		if (!$this->codeValid($code)){

		    throw new UserException(ERR::GOOGLE_BADLOGIN, 'Login invalid. Could not verify your account with Google');
		}

		// Payload contains user data from Google
		$payload = $this->getPayload();

		// Get Google+ Plus user info
		$userinfo = $this->getUserInfo();

		if (!isset($payload['email']) || !isset($payload['sub'])){

			throw new UserException(ERR::GOOGLE_NODATA, 'Error getting payload');
		}

		$user = \Users::findFirstByEmail($payload['email']);

		// No user found? Register Google Account
		if (!$user){

			$user = $this->register($payload, $userinfo);
		}

		// When the user has last logged in with his UsernameAccount
		// the user above is found but we want to change the account id
		// to the AUTH_ACCOUNTTYPE_GOOGLE, but first we check if the GoogleAccount
		// If not, we create one based on the payload.
		if (!$user->hasAccountType(AccountTypes::GOOGLE)){

			$googleAccount = \GoogleAccounts::findFirstByEmail($payload['email']);

			if (!$googleAccount){
				$googleAccount             	= new \GoogleAccounts();
				$googleAccount->userId   	= $user->id;
				$googleAccount->googleId   	= $userinfo['id'];
				$googleAccount->email      	= $payload['email'];

				if (!$googleAccount->save()){
				    throw new CoreException(ERR::USER_REGISTERFAIL, 'GoogleAccount for user #' . $user->id . ' could not be created');
				}
			}

			$user->addAccountType(AccountTypes::GOOGLE);
			$user->save();
		}

		return $user;
	}
}
