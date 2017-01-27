<?php

namespace GoPague\Http;

use GoPague\Credential;
use GuzzleHttp\Client as HttpClient;

/**
 * GoPague API Client.
 *
 * Use to authenticate and make requests to GoPague API:
 *
 *    // login to API (following API calls will use the returned autenticated credenciais
 *    Client::login($email, $passord);
 *    // get all departaments (already use credentials)
 *    Client::get('departaments');
 *    // create a new client (already use credentials)
 *    Client::post('clients', $data);
 */
trait ProxyMethods
{
    protected static $email;
    protected static $password;

    protected static $httpConfig = null;
    protected static $instance;

    /**
     * Set the email field from credential
     *
     * @param string $email
     * @return self
     */
    public static function setEmail(string $email)
    {
        static::$email = $email;
    }

    /**
     * Set the password field from credential
     *
     * @param string $password
     * @return self
     */
    public static function setPassword(string $password)
    {
        static::$password = $password;
    }

    /**
     * Set API Base URI
     *
     * @param string $baseUri
     * @return self
     */
    public static function setBaseUri(string $baseUri)
    {
        static::$baseUri = $baseUri;
    }

    /**
     * Set Http Config from Guzzle Client
     *
     * @see GuzzleHttp\Client
     *
     * @param string $baseUri
     * @return self
     */
    public static function setHttpConfig(array $config)
    {
        static::$httpConfig = $config;
    }

    /**
     * If authenticated, returns the authenticated credenciais,
     * otherwise returns null
     *
     * @return credential|null
     */
    public static function credential()
    {
        return static::$instance->credential;
    }


    /**
     * Logins to Go Pague API.
     * Static easy access to self::attemptLogin.
     *
     * @usage
     *    Client::login($email, $password);
     *
     * @see self::attemptLogin
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return Credential
     */
    public static function login(string $email, string $password) : Credential
    {
        $httpConfig = is_null(static::$httpConfig) ?
            ['base_uri' => self::BASE_URI] :
            static::$httpConfig
        ;

        static::$instance = new static(new HttpClient($httpConfig));

        return static::$instance->attemptLogin($email, $password);
    }


    /**
     * Handle dynamic static method calls into the method to redirect
     * to self::requestServer.
     * Same as self::__call, but statically.
     * @see self::__call
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (!static::$instance) {
            self::login(static::$email, static::$password);
        }

        return call_user_func_array([static::$instance, $method], $parameters);
    }
}
