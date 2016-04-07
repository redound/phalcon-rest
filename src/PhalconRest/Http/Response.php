<?php

namespace PhalconRest\Http;

use PhalconRest\Constants\Services;
use PhalconRest\Exception;

class Response extends \Phalcon\Http\Response
{
    public function setErrorContent(\Exception $e, $developerInfo = false)
    {
        /** @var Request $request */
        $request = $this->getDI()->get(Services::REQUEST);

        /** @var \PhalconRest\Helpers\ErrorHelper $errorHelper */
        $errorHelper = $this->getDI()->has(Services::ERROR_HELPER) ? $this->getDI()->get(Services::ERROR_HELPER) : null;

        $errorCode = $e->getCode();
        $statusCode = 500;
        $message = $e->getMessage();

        if ($errorHelper && $errorHelper->has($errorCode)) {

            $defaultMessage = $errorHelper->get($errorCode);

            $statusCode = $defaultMessage['statusCode'];

            if (!$message) {
                $message = $defaultMessage['message'];
            }
        }

        $error = [
            'code' => $errorCode,
            'message' => $message ?: 'Unspecified error',
        ];

        if ($e instanceof Exception && $e->getUserInfo() != null) {
            $error['info'] = $e->getUserInfo();
        }

        if ($developerInfo === true) {

            $developerResponse = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->getMethod() . ' ' . $request->getURI()
            ];

            if ($e instanceof Exception && $e->getDeveloperInfo() != null) {
                $developerResponse['info'] = $e->getDeveloperInfo();
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
