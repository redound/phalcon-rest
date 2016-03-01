<?php

namespace PhalconRest\Http;

use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\Services;
use PhalconRest\Exception;

class Response extends \Phalcon\Http\Response
{
    protected $defaultErrorMessages = [

        // General
        ErrorCodes::GENERAL_SYSTEM => [
            'statusCode' => 500,
            'message' => 'General: System Error'
        ],

        ErrorCodes::GENERAL_NOT_IMPLEMENTED => [
            'statusCode' => 500,
            'message' => 'General: Not Implemented'
        ],

        ErrorCodes::GENERAL_NOT_FOUND => [
            'statusCode' => 404,
            'message' => 'General: Not Found'
        ],

        // Authentication
        ErrorCodes::AUTH_INVALID_ACCOUNT_TYPE => [
            'statusCode' => 400,
            'message' => 'Authentication: Invalid Account Type'
        ],

        ErrorCodes::AUTH_LOGIN_FAILED => [
            'statusCode' => 401,
            'message' => 'Authentication: Login Failed'
        ],

        ErrorCodes::AUTH_TOKEN_INVALID => [
            'statusCode' => 401,
            'message' => 'Authentication: Login Failed'
        ],

        ErrorCodes::AUTH_SESSION_EXPIRED => [
            'statusCode' => 401,
            'message' => 'Authentication: Session Expired'
        ],

        ErrorCodes::AUTH_SESSION_INVALID => [
            'statusCode' => 401,
            'message' => 'Authentication: Session Invalid'
        ],

        // Access Control
        ErrorCodes::ACCESS_DENIED => [
            'statusCode' => 403,
            'message' => 'Access: Denied'
        ],

        // Data
        ErrorCodes::DATA_FAILED => [
            'statusCode' => 500,
            'message' => 'Data: Failed'
        ],

        ErrorCodes::DATA_NOT_FOUND => [
            'statusCode' => 404,
            'message' => 'Data: Not Found'
        ],

        ErrorCodes::POST_DATA_NOT_PROVIDED => [
            'statusCode' => 400,
            'message' => 'Postdata: Not provided'
        ],

        ErrorCodes::POST_DATA_INVALID => [
            'statusCode' => 400,
            'message' => 'Postdata: Invalid'
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

    public function setErrorContent(\Exception $e, $developerInfo = false)
    {
        /** @var Request $request */
        $request = $this->getDI()->get(Services::REQUEST);

        $errorCode = $e->getCode();
        $statusCode = 500;
        $message = $e->getMessage();

        if (array_key_exists($errorCode, $this->defaultErrorMessages)) {

            $defaultMessage = $this->defaultErrorMessages[$errorCode];

            $statusCode = $defaultMessage['statusCode'];

            if(!$message) {
                $message = $defaultMessage['message'];
            }
        }

        $error = [
            'code' => $errorCode,
            'message' => $message ?: 'Unspecified error',
        ];

        if ($developerInfo === true) {

            $developerResponse = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->getMethod() . ' ' . $request->getURI()
            ];

            if($e instanceof Exception && $e->getInfo() != null){
                $developerResponse['info'] = $e->getInfo();
            }

            $error['developer'] = $developerResponse;
        }

        $this->setJsonContent(['error' => $error]);
        $this->setStatusCode($statusCode);
    }

    public function setJsonContent($content, $jsonOptions = 0, $depth = 512)
    {
        parent::setJsonContent($content, $jsonOptions, $depth);

        $this->setContentType('application/json', 'UTF-8');
        $this->setHeader('E-Tag', md5($this->getContent()));
    }
}