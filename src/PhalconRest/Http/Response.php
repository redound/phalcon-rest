<?php

namespace PhalconRest\Http;


class Response extends \Phalcon\Http\Response
{
    protected $defaultErrorMessages = [

        // General
        1001 => [
           'statuscode' => 404,
           'message' => 'General: Not found',
        ],

        // Data
        2001 => [
           'statuscode' => 404,
           'message' => 'Data: Duplicate data',
        ],

        2002 => [
           'statuscode' => 404,
           'message' => 'Data: Not Found',
        ],

        2003 => [
           'statuscode' => 404,
           'message' => 'Failed to process data',
        ],

        2004 => [
           'statuscode' => 404,
           'message' => 'Data: Invalid',
        ],

        2005 => [
           'statuscode' => 404,
           'message' => 'Action failed',
        ],

        2010 => [
           'statuscode' => 404,
           'message' => 'Data: Not Found',
        ],

        2020 => [
           'statuscode' => 500,
           'message' => 'Data: Failed to create',
        ],

        2030 => [
           'statuscode' => 500,
           'message' => 'Data: Failed to update',
        ],

        2040 => [
           'statuscode' => 500,
           'message' => 'Data: Failed to delete',
        ],

        2060 => [
           'statuscode' => 404,
           'message' => 'Data: Rejected',
        ],

        2070 => [
           'statuscode' => 403,
           'message' => 'Data: Action not allowed',
        ],

        // Authentication
        3006 => [
           'statuscode' => 400,
           'message' => 'Auth: Provided token invalid',
        ],

        3007 => [
           'statuscode' => 404,
           'message' => 'Auth: No username present',
        ],

        3008 => [
           'statuscode' => 404,
           'message' => 'Auth: Invalid authentication bearer type',
        ],

        3009 => [
           'statuscode' => 404,
           'message' => 'Auth: Bad login credentials',
        ],

        3010 => [
           'statuscode' => 401,
           'message' => 'Auth: Unauthorized',
        ],

        3020 => [
           'statuscode' => 403,
           'message' => 'Auth: Forbidden',
        ],

        3030 => [
           'statuscode' => 401,
           'message' => 'Auth: Session expired',
        ],

        4001 => [
           'statuscode' => 404,
           'message' => 'Google: No data',
        ],

        4002 => [
           'statuscode' => 404,
           'message' => 'Google: Bad login',
        ],

        4003 => [
           'statuscode' => 404,
           'message' => 'User: Not active',
        ],

        4004 => [
           'statuscode' => 404,
           'message' => 'User: Not found',
        ],

        4005 => [
           'statuscode' => 404,
           'message' => 'User: Registration failed',
        ],

        4006 => [
           'statuscode' => 404,
           'message' => 'User: Modification failed',
        ],

        4007 => [
           'statuscode' => 404,
           'message' => 'User: Creation failed',
        ],

        // PDO
        23000 => [
           'statuscode' => 404,
           'message' => 'Duplicate entry',
        ]
    ];


    public function getDefaultErrorMessages()
    {
        return $this->defaultErrorMessages;
    }

    public function setDefaultErrorMessages($messages)
    {
        $this->defaultErrorMessages = $messages;
    }

    public function setErrorContent(\Exception $e, $developerInfo=false)
    {
        $errorCode = $e->getCode();
        $statusCode = 500;
        $message = 'Unspecified error';

        if(array_key_exists($errorCode, $this->defaultErrorMessages)){

            $defaultMessage = $this->defaultErrorMessages[$errorCode];

            $statusCode = $defaultMessage['statuscode'];
            $message = $defaultMessage['message'];
        }

        $error = [
            'code' => $errorCode,
            'status' => $statusCode,
            'message' => $message,
        ];

        if ($developerInfo === true) {

            $error['developer'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage(),
            ];
        }

        $this->setJsonContent(['error' => $error]);
        $this->setStatusCode($statusCode);
    }

    public function setJsonContent($content, $jsonOptions = 0, $depth = 512)
    {
        parent::setJsonContent($content, $jsonOptions, $depth);

        $this->setHeader('Access-Control-Allow-Origin', '*');
        $this->setHeader('Access-Control-Allow-Methods', 'GET,HEAD,PUT,PATCH,POST,DELETE');
        $this->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        $this->setHeader('E-Tag', md5($this->getContent()));
        $this->setContentType('application/json');
    }
}