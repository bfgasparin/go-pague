<?php

namespace GoPague\Http;

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
class Client
{
    use ProxyMethods;

    protected $httpClient;
    public $credential;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
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
     *    $credential = $client->attemptLogin($email, $passord);
     *    // get all departaments (already use credentials)
     *    $client->get('departaments');
     *    // create a new client (already use credentials)
     *    $client->post('clients', $data);
     *
     * @param string $email
     * @param string $password
     */
    public function attemptLogin(string $email, string $password) : Credential
    {
        return $this->credential = Credential::create($email, $password);
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
     *    $client->login($email, $passord);
     *    // get all departaments (uses auth credentials)
     *    $client->requestServer('get', 'departaments');
     *    // create a new client (uses auth credentials)
     *    $client->requestServer('post', 'clients', $data);
     *
     *    Or you can the magic method of GoPague that
     *    represents the HTTP METHODs (get, post, put, head, delete, ...)
     *
     *    // get all departaments (uses auth credentials)
     *    $client->get('departaments');
     *    // create a new client (uses auth credentials)
     *    $client->post(clients', $data);
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
     * Handle dynamic method calls to redirect to self::requestServer.
     *
     * A call to:
     *    $client->get('departaments');
     * Is equivalent to:
     *    $client->requestServer('get', 'departaments');
     *
     * A call to:
     *    $client->post(clients', $data);
     * Is equivalent to:
     *    $client->requestServer('post', 'clients', $data);
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
