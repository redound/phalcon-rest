<?php

namespace OA\PhalconRest\Services;

class ErrorService
{
	// General
	const GEN_NOTFOUND 			= 1001;

	// Data
	const DATA_DUPLICATE 		= 2001;
	const DATA_NOTFOUND 		= 2002;
	const DATA_UNPROCESSABLE 	= 2003;
	const DATA_INVALID 			= 2004;
	const DATA_FAIL 			= 2005;

	// Authentication
	const AUTH_NOBEARER			= 3006;
	const AUTH_NOUSERNAME	    = 3007;
	const AUTH_INVALIDTYPE 		= 3008;
	const AUTH_BADLOGIN 		= 3009;
	const AUTH_UNAUTHORIZED 	= 3010;
	const AUTH_FORBIDDEN 		= 3020;

	// Google
	const GOOGLE_NODATA			= 4001;
	const GOOGLE_BADLOGIN		= 4002;

	// User management
	const USER_NOTACTIVE		= 4003;
	const USER_NOTFOUND			= 4004;
	const USER_REGISTERFAIL		= 4005;
	const USER_MODFAIL			= 4006;
	const USER_CREATEFAIL		= 4007;

	// PDO
	const PDO_DUPLICATE_ENTRY   = 2300;

	protected static function getMessages()
	{

		return [
			// General
			1001 => [
				'statuscode' => 404,
				'message' => 'General: Not found'
			],

			// Data
			2001 => [
				'statuscode' => 404,
				'message' => 'Data: Duplicate data'
			],

			2002 => [
				'statuscode' => 404,
				'message' => 'Data: Not Found'
			],

			2003 => [
				'statuscode' => 404,
				'message' => 'Failed to process data'
			],

			2004 => [
				'statuscode' => 404,
				'message' => 'Data: Invalid'
			],

			2005 => [
				'statuscode' => 404,
				'message' => 'Action failed'
			],

			// Authentication
			3006 => [
				'statuscode' => 404,
				'message' => 'Auth: No authentication bearer present'
			],

			3007 => [
				'statuscode' => 404,
				'message' => 'Auth: No username present'
			],

			3008 => [
				'statuscode' => 404,
				'message' => 'Auth: Invalid authentication bearer type'
			],

			3009 => [
				'statuscode' => 404,
				'message' => 'Auth: Bad login credentials'
			],

			3010 => [
				'statuscode' => 401,
				'message' => 'Auth: Unauthorized'
			],

			3020 => [
				'statuscode' => 403,
				'message' => 'Auth: Forbidden'
			],

			4001 => [
				'statuscode' => 404,
				'message' => 'Google: No data'
			],

			4002 => [
				'statuscode' => 404,
				'message' => 'Google: Bad login'
			],

			4003 => [
				'statuscode' => 404,
				'message' => 'User: Not active'
			],

			4004 => [
				'statuscode' => 404,
				'message' => 'User: Not found'
			],

			4005 => [
				'statuscode' => 404,
				'message' => 'User: Registration failed'
			],

			4006 => [
				'statuscode' => 404,
				'message' => 'User: Modification failed'
			],

			4007 => [
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
