<?php

namespace GoPague;

use GoPague\Credential;
use GoPague\Exceptions\ConnectionException;
use GoPague\Exceptions\RequestException as RequestException;
use GoPague\Exceptions\ResourceNotFoundException;
use GoPague\Exceptions\ValidationException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * GoPague API Wrapper.
 *
 * Use to authenticate and make requests to GoPague API:
 *
 *    // login to API (following API calls will use the returned autenticated credenciais
 *    GoPague::login($email, $passord);
 *    // get all departaments (already use credentials)
 *    GoPague::get('departaments');
 *    // create a new client (already use credentials)
 *    GoPague::post('clients', $data);
 */
class GoPague
{
    const BASE_URI = 'http://portal-staging.redepagnet.com/api/';

    protected $httpClient;
    public $credential;

    protected static $email;
    protected static $password;

    protected static $httpConfig = null;

    /**
     * @var array  Credenciais for GoPague Staging environment
     */
    protected static $stagingCredentials;
    protected static $instance;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

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
     * Set the email field from credential
     *
     * @param string $email
     * @return self
     */
    public static function setStagingCredentials(string $username, string $password)
    {
        static::$stagingCredentials = compact('username', 'password');
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
     * Login to Go Pague API with the given parameters.
     * If success the authenticated token is achieved with
     * self::credentials()
     * @see self::credential()
     *
     * After call this method, you can request other resources
     * that GoPague will automatically use the autenticated credentials.
     *
     * @example:
     *
     *    $goPague->login($email, $passord);
     *    // get all departaments (already use credentials)
     *    $goPague->get('departaments');
     *    // create a new client (already use credentials)
     *    $goPague->post('clients', $data);
     *
     * @param string $email
     * @param string $password
     */
    public function attemptLogin(string $email, string $password) : Credential
    {
        return $this->credential = Credential::create($email, $password);
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
     *    GoPague::login($email, $password);
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
     * Make a Request to Go Pague Server and returns a Go Pague
     * respective object
     *
     * To use this method, you need first authenticate to API, using
     * self::attemptLogin
     *
     * @example:
     *
     *    $goPague->login($email, $passord);
     *    // get all departaments (uses auth credentials)
     *    $goPague->requestServer('get', 'departaments');
     *    // create a new client (uses auth credentials)
     *    $goPague->requestServer('post', 'clients', $data);
     *
     *    Or you can the magic method of GoPague that
     *    represents the HTTP METHODs (get, post, put, head, delete, ...)
     *
     *    // get all departaments (uses auth credentials)
     *    $goPague->get('departaments');
     *    // create a new client (uses auth credentials)
     *    $goPague->post(clients', $data);
     *
     * @param string $method  The HTTP method to use
     * @param string $uri     The uri to use
     * @param string $body    The body to send, if there is any
     * @return mixed
     * @thrown GoPague\GoPagueExceptionInterface When an error with GoPague communication occurs
     */
    public function requestServer(string $method, string $uri, string $body = null)
    {
        // data
        $headers = [
            'Content-Type' => 'application/json',
            'ACCEPT' => 'version=1',
        ];
        $options = [];

        // fill data according to the enviroment
        if ($this->credential) {
            $headers['AUTHORIZATION'] = 'Token token='.$this->credential->token;
        }

        /**
         * Go Pague ues explict headers to authenticate using HTTP Basic Auth,
         * instead od the Authentication header.
         *
         * It lets Authentcation header for the Token authentication
         */
        if ($method === 'get' && static::$stagingCredentials) {
            $headers['HTTP_BASIC_AUTH_USER'] = static::$stagingCredentials['username'];
            $headers['HTTP_BASIC_AUTH_PASSWORD'] = static::$stagingCredentials['password'];
        }

        // make the request
        try {
            $request = new Request($method, $uri, $headers, $body);
            $response = $this->httpClient->send($request, $options);

            return json_decode(
                $response->getBody()->getContents(),
                true
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $content = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 422) {
                throw new ValidationException(
                    $content['message'],
                    $e->getRequest(),
                    $e->getResponse()
                );
            } elseif ($response->getStatusCode() == 404) {
                throw new ResourceNotFoundException($e->getMessage(), $e->getRequest(), $e->getResponse());
            }
            throw RequestException::serviceRespondedWithAnError($e->getRequest(), $e->getResponse(), $e);
        } catch (ConnectException $e) {
            throw ConnectionException::couldNotConnectToService($e->getRequest(), $e);
        } catch (GuzzleRequestException $e) {
            throw RequestException::serviceRespondedWithAnError($e->getRequest(), $e->getResponse(), $e);
        }
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

    /**
     * Handle dynamic method calls to redirect to self::requestServer.
     *
     * A call to:
     *    $goPague->get('departaments');
     * Is equivalent to:
     *    $goPague->requestServer('get', 'departaments');
     *
     * A call to:
     *    $goPague->post(clients', $data);
     * Is equivalent to:
     *    $goPague->requestServer('post', 'clients', $data);
     *
     * And so on ...
     *
     * @param  string  $method   The HTTP method
     * @param  array  $parameters  The rest of self::requestServer parameters
     *
     * @return mixed
     * @thrown GoPague\GoPagueExceptionInterface When an error with GoPague communication occurs
     */
    public function __call(string $method, array $parameters)
    {
        list($uri, $body) = array_pad($parameters, 2, null);

        $body = !is_null($body) ? json_encode($body) : null;

        return $this->requestServer(
            $method,
            $uri,
            $body
        );
    }
}
