<?php

namespace PhalconRest\Facades;

class GoogleClient
{
    /**
     * Google_Client instance.
     *
     * @var object
     */
    protected $client;

    /**
     * Google access token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Creates a new instance of the GoogleClient instance.
     *
     * @param object $client
     * @return self
     */
    public function __construct($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set the Google_Client instance.
     *
     * @param object $client
     * @return self
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the Google_Client instance.
     *
     * @return object
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the Client ID provided by Google.
     *
     * @param string $id
     * @return self
     */
    public function setClientId($id)
    {
        $this->client->setClientId($id);

        return $this;
    }

    /**
     * Set the Client secret provided by Google.
     *
     * @param string $secret
     * @return self
     */
    public function setClientSecret($secret)
    {
        $this->client->setClientSecret($secret);

        return $this;
    }

    /**
     * Set the Redirect URI.
     *
     * @param string $uri
     * @return self
     */
    public function setRedirectUri($uri)
    {
        $this->client->setRedirectUri($uri);

        return $this;
    }

    /**
     * Set the scopes.
     *
     * @param string $scopes
     * @return self
     */
    public function setScopes($scopes)
    {
        $this->client->setScopes($scopes);

        return $this;
    }

    /**
     * Set the Access Token.
     *
     * @param string $token
     * @return self
     */
    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);

        return $this;
    }

    /**
     * Get the Access Token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }

    /**
     * Authenticate with Google using
     * previously received code
     *
     * @return bool
     * @param string $code
     */
    public function authenticate($code)
    {
        try {

            $this->client->authenticate($code);

        } catch (\Exception $e) {

            return false;
        }

        $accessToken = $this->getAccessToken();
        $this->setAccessToken($accessToken);
        return true;
    }

    /**
     * Get the payload.
     *
     * @return array
     */
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

    /**
     * Parse payload received from Google
     *
     * @param array$payload
     * @return array
     */
    protected function parsePayload($payload)
    {
        try {

            $payload = [
                'email' => $payload['email'],
                'googleId' => $payload['sub'],
            ];
        } catch (\Exception $e) {

            return false;
        }

        return $payload;
    }
}
