<?php

namespace PhalconRest\Mailer;

interface Mailer
{

	public function __construct($mailer);
	public function setSubject($subject);
	public function addAddress($name, $email);
	public function setHtmlBody($body);
	public function send();
}
