<?php

namespace OA\PhalconRest\Services;

class ErrorService
{
	// General
	const GEN_NOTFOUND 			= 'gen-notfound';

	// Data
	const DATA_DUPLICATE 		= 'data-duplicate';
	const DATA_NOTFOUND 		= 'data-notfound';
	const DATA_UNPROCESSABLE 	= 'data-unprocessable';
	const DATA_INVALID 			= 'data-invalid';
	const DATA_FAIL 			= 'data-fail';

	// Authentication
	const AUTH_NOBEARER			= 'auth-nobearer';
	const AUTH_NOUSERNAME	    = 'auth-nousername';
	const AUTH_INVALIDTYPE 		= 'auth-invalidtype';
	const AUTH_BADLOGIN 		= 'auth-badlogin';
	const AUTH_UNAUTHORIZED 	= 'auth-unauthorized';
	const AUTH_FORBIDDEN 		= 'auth-forbidden';

	// Google
	const GOOGLE_NODATA			= 'google-nodata';
	const GOOGLE_BADLOGIN		= 'google-badlogin';

	// User management
	const USER_NOTACTIVE		= 'user-notactive';
	const USER_NOTFOUND			= 'user-notfound';
	const USER_REGISTERFAIL		= 'user-registerfail';
	const USER_MODFAIL			= 'user-modfail';
	const USER_CREATEFAIL		= 'user-createfail';

	// PDO
	const PDO_DUPLICATE_ENTRY   = 2300;

	protected static function getMessages()
	{

		return [
			// General
			'gen-notfound' => [
				'statuscode' => 404,
				'message' => 'General: Not found'
			],

			// Data
			'data-duplicate' => [
				'statuscode' => 404,
				'message' => 'Data: Duplicate data'
			],

			'data-notfound' => [
				'statuscode' => 404,
				'message' => 'Data: Not Found'
			],

			'data-unprocessable' => [
				'statuscode' => 404,
				'message' => 'Failed to process data'
			],

			'data-invalid' => [
				'statuscode' => 404,
				'message' => 'Data: Invalid'
			],

			'data-fail' => [
				'statuscode' => 404,
				'message' => 'Action failed'
			],

			// Authentication
			'auth-nobearer' => [
				'statuscode' => 404,
				'message' => 'Auth: No authentication bearer present'
			],

			'auth-invalidtype' => [
				'statuscode' => 404,
				'message' => 'Auth: Invalid authentication bearer type'
			],

			'auth-nousername' => [
				'statuscode' => 404,
				'message' => 'Auth: No username present'
			],

			'auth-badlogin' => [
				'statuscode' => 404,
				'message' => 'Auth: Bad login credentials'
			],

			'auth-unauthorized' => [
				'statuscode' => 401,
				'message' => 'Auth: Unauthorized'
			],

			'auth-forbidden' => [
				'statuscode' => 403,
				'message' => 'Auth: Forbidden'
			],

			'google-nodata' => [
				'statuscode' => 404,
				'message' => 'Google: No data'
			],

			'google-badlogin' => [
				'statuscode' => 404,
				'message' => 'Google: Bad login'
			],

			'user-notactive' => [
				'statuscode' => 404,
				'message' => 'User: Not active'
			],

			'user-notfound' => [
				'statuscode' => 404,
				'message' => 'User: Not found'
			],

			'user-registerfail' => [
				'statuscode' => 404,
				'message' => 'User: Registration failed'
			],

			'user-modfail' => [
				'statuscode' => 404,
				'message' => 'User: Modification failed'
			],

			'user-createfail' => [
				'statuscode' => 404,
				'message' => 'User: Creation failed'
			],

			// PDO
			23000 => [
				'statuscode' => 404,
				'message' => 'Duplicate entry'
			],
		];
	}

	public static function getMessage($key)
	{

		$messages = self::getMessages();

		if (!isset($messages[$key])) {

			return 'No error message specified';
		}

		return $messages[$key]['message'];
	}

	public static function getStatusCode($key)
	{

		$messages = self::getMessages();

		if (!isset($messages[$key])) {

			return 404;
		}

		return $messages[$key]['statuscode'];
	}
}
