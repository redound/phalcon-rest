<?php

namespace PhalconRest\Auth;

interface Session 
{
	public function encode($token);
	public function decode($token);
	public function create($issuer, $user, $iat, $exp);
}