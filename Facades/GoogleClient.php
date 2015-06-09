<?php

namespace PhalconRest\Facades;

class GoogleClient
{
	protected $client;
	protected $accessToken;

	public function __construct($client)
	{
		$this->client = $client;

		return $this;
	}

	public function setClient($client)
	{
		$this->client = $client;

		return $this;
	}

	public function getClient()
	{
		return $this->client;
	}

	public function setClientId($id)
	{
		$this->client->setClientId($id);

		return $this;
	}

	public function setClientSecret($secret)
	{
		$this->client->setClientSecret($secret);

		return $this;
	}

	public function setRedirectUri($uri)
	{
		$this->client->setRedirectUri($uri);

		return $this;
	}

	public function setScopes($scopes)
	{
		$this->client->setScopes($scopes);

		return $this;
	}

	public function setAccessToken($token)
	{
		$this->client->setAccessToken($token);

		return $this;
	}

	public function getAccessToken()
	{
		return $this->client->getAccessToken();
	}

	public function authenticate($code)
	{
		try {

			$this->client->authenticate($code);

		} catch (\Exception $e) {

			echo $e->getMessage();
			exit;

			return false;
		}

		$accessToken = $this->getAccessToken();
		$this->setAccessToken($accessToken);
		return true;
	}

	public function getPayload()
	{
		try {

			$payload = $this->client
				->verifyIdToken()
				->getAttributes()['payload'];

			$payload = $this->parsePayload($payload);

		} catch (\Exception $e) {

			return false;
		}

		return $payload;
	}

	protected function parsePayload($payload)
	{
		try {

			$payload = [
				'email' => $payload['email'],
				'googleId' => $payload['sub']
			];
		} catch (\Exception $e) {

			return false;
		}

		return $payload;
	}
}