<?php

namespace PhalconRest\Constants;

class ErrorCodes
{
    // General
    const GEN_SYSTEM = 0;
    const GEN_NOTFOUND = 1001;

    // Data
    const DATA_DUPLICATE = 2001;
    const DATA_NOTFOUND = 2002;
    const DATA_INVALID = 2004;
    const DATA_FAIL = 2005;

    const DATA_FIND_FAIL = 2010;
    const DATA_CREATE_FAIL = 2020;
    const DATA_UPDATE_FAIL = 2030;
    const DATA_DELETE_FAIL = 2040;
    const DATA_REJECTED = 2060;
    const DATA_NOTALLOWED = 2070;

    // Authentication
    const AUTH_BADTOKEN = 3006;
    const AUTH_NOUSERNAME = 3007;
    const AUTH_INVALIDTYPE = 3008;
    const AUTH_BADLOGIN = 3009;
    const AUTH_UNAUTHORIZED = 3010;
    const AUTH_FORBIDDEN = 3020;
    const AUTH_EXPIRED = 3030;

    // User management
    const USER_NOTACTIVE = 4003;
    const USER_NOTFOUND = 4004;
    const USER_REGISTERFAIL = 4005;
    const USER_MODFAIL = 4006;
    const USER_CREATEFAIL = 4007;

    // PDO
    const PDO_DUPLICATE_ENTRY = 2300;
}