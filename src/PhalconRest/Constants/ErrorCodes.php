<?php

namespace PhalconRest\Constants;

class ErrorCodes
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

}