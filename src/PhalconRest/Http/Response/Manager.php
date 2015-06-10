<?php

namespace PhalconRest\Http\Response;

class Manager
{
    protected $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getMessage($key)
    {
        $messages = $this->getMessages();

        if (!isset($messages[$key])) {

            return 'No error message specified';
        }

        return $messages[$key]['message'];
    }

    public function getStatusCode($key)
    {

        $messages = $this->getMessages();

        if (!isset($messages[$key])) {

            return 404;
        }

        return $messages[$key]['statuscode'];
    }
}
