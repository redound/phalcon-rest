<?php

namespace OA\PhalconRest\Services;

use OA\PhalconRest\UserException,
	OA\PhalconRest\Services\ErrorService as ERR,
	Google_Client;

class AuthService extends \Phalcon\Mvc\User\Plugin {

	protected $_bearer = null;
	protected $_user = null;

	public function __construct()
	{

		require_once $this->config->application->googleAutoloaderPath;
		$googleClient = new Google_Client;

		$this->authGoogle = new AuthGoogle($googleClient);
		$this->authUsername = new AuthUsername;
	}

	public function setUser($user)
	{

		$this->_user = $user;
	}

	public function getUser()
	{

		return $this->_user;
	}

	public function loggedIn()
	{

		return !is_null($this->_user);
	}

	public function login($bearer, $username, $password)
	{

		$this->_bearer = $bearer;

		switch($bearer){

			case AUTHTYPE_GOOGLE:
				$user = $this->authGoogle->login($username);
				break;
			case AUTHTYPE_USERNAME:
				$user = $this->authUsername->login($username, $password);
				break;
			default:
				throw new UserException(ERR::AUTH_INVALIDTYPE);
				break;
		}

		if (!$user){

			throw new UserException(ERR::AUTH_BADLOGIN);
		}

		$this->setUser($user);

		return $user;

	}

	public function createToken()
	{

		return array(

		    /*
			The iss (issuer) claim identifies the principal
			that issued the JWT. The processing of this claim
			is generally application specific.
			The iss value is a case-sensitive string containing
			a StringOrURI value. Use of this claim is OPTIONAL.
		    ------------------------------------------------*/
		    "iss" => $this->_bearer,

		    /*
			The sub (subject) claim identifies the principal
			that is the subject of the JWT. The Claims in a
			JWT are normally statements about the subject.
			The subject value MUST either be scoped to be
			locally unique in the context of the issuer or
			be globally unique. The processing of this claim
			is generally application specific. The sub value
			is a case-sensitive string containing a
			StringOrURI value. Use of this claim is OPTIONAL.
		    ------------------------------------------------*/
		    "sub" => $this->_user,

		    /*
		    The iat (issued at) claim identifies the time at
		    which the JWT was issued. This claim can be used
		    to determine the age of the JWT. Its value MUST
		    be a number containing a NumericDate value.
		    Use of this claim is OPTIONAL.
		    ------------------------------------------------*/
		    "iat" => time() * 1000,

		    /*
		    The exp (expiration time) claim identifies the
		    expiration time on or after which the JWT MUST NOT
		    be accepted for processing. The processing of the
		    exp claim requires that the current date/time MUST
		    be before the expiration date/time listed in the
		    exp claim. Implementers MAY provide for some small
		    leeway, usually no more than a few minutes,
		    to account for clock skew. Its value MUST be a
		    number containing a NumericDate value.
		    Use of this claim is OPTIONAL.
		    ------------------------------------------------*/
		    // Now + one week
		    "exp" => (time() * 1000) + 604800000
		);
	}
}
