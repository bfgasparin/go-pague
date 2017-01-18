<?php

namespace GoPague;

use GoPague\Credential;
use GoPague\Exceptions\GoPagueException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * GoPague API Wrapper
 */
class GoPague
{
    const API_ENDPOINT = 'http://portal-staging.redepagnet.com';
    /* const API_ENDPOINT = 'https://private-56fc7-pagnet2.apiary-mock.com'; */

    protected $httpClient;
    protected $apiToken;

    protected static $email;
    protected static $password;
    protected static $instance;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->apiToken = null;
    }

    /**
     * Set the email field from credential
     *
     * @param string $email
     * @return self
     */
    public static function setEmail(string $email) : self
    {
        static::$email = $email;
    }

    /**
     * Set the password field from credencial
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password) : self
    {
        static::$password = $password;
    }

    public function attemptLogin(string $email, string $password)
    {
        $body = [
            'user' => ['email' => $email, 'password' => $password]
        ];

        $content = $this->requestServer(
            'post',
            self::API_ENDPOINT . '/users/sign_in',
            json_encode($body)
        );

        return new Credential($content);
    }


    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function login(string $email, string $password)
    {
        static::$instance = new static(new HttpClient());

        return static::$instance->attemptLogin($email, $password);
    }

    /**
     * Handle dynamic static method calls into the method
     * to request to GoPague server
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if (!$this-apiToken) {
            $this->login(static::$email, static::$password);
        }

        return $this->requestServer($method, $parameters);
    }

    protected function requestServer($method, $url, $body)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'ACCEPT' => 'version=1',
        ];

        $request = new Request($method, $url, $headers, $body);

        try {
            $response = $this->httpClient->send($request, ['auth' => ['admin@pag.net', 'pagnet2017']]);

            return json_decode(
                $response->getBody()->getContents(),
                true
            );
        } catch (RequestException $e) {
            throw GoPagueException::serviceRespondedWithAnError($e->getRequest(), $e->getResponse(), $e);
        }
    }
}
