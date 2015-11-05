<?php

namespace PhalconRest\Http;


class Response extends \Phalcon\Http\Response
{
    protected $defaultErrorMessages = [];

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