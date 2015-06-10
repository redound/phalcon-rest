<?php

namespace PhalconRest\Mailer\Adapter;

class PhpMailer extends \Phalcon\Mvc\User\Plugin implements \PhalconRest\Mailer\Mailer
{
    protected $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;

        return $this;
    }

    public function addAddress($name, $email)
    {
        $this->mailer->addAddress($name, $email);

        return $this;
    }

    public function setHtmlBody($body)
    {
        $this->mailer->msgHtml($body);

        return $this;
    }

    public function send()
    {
        $this->mailer->Send();

        return $this;
    }
}
