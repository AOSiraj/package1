<?php namespace package1\Service;
/**
 * package1\Service\HttpRequestService
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/3/2017
 * Time: 12:53 PM
 */

use Zend\Http\Client;
use package1\Service\HttpRequestListnerService;

class HttpRequestService
{
    protected $AuthenticationService;

    /**
     * HttpRequestService constructor.
     * @param $AuthenticationService
     */
    public function __construct($AuthenticationService)
    {
        $this->AuthenticationService = $AuthenticationService;
    }

    /**
     * @return mixed
     */
    public function getToken(){
        return $this->AuthenticationService->getToken();
    }

    /**
     * @return array
     */
    public function getHeader(){
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $this->getToken(),
        );
        return $headers;
    }

    /**
     * @param $uri
     * @param array $params
     * @param null $header
     * @return mixed|null
     */
    public function get($uri, $params=array(), $header = null){
        if ($header  == null)
            $header = $this->getHeader();

        $requestcount = new HttpRequestListnerService();
        $requestcount->varDumpRequest($uri);
        $client = new Client();
        $client->setUri($uri);
        $client->setHeaders($header);
        $client->setParameterGet($params);
        $response = $client->send();
        if($response->isSuccess()){
            $decodedResponse = json_decode($response->getBody(), true);;
            return $decodedResponse;
        }
        else
            return null;

    }

    /**
     * @param $uri
     * @param $body
     * @param null $header
     * @return mixed
     */
    public function post($uri, $body, $header = null){
        if ($header  == null)
            $header = $this->getHeader();
        $client = new Client();
        $client->setUri($uri);
        $client->setHeaders($header);
        $client->setMethod('POST');
        $body = json_encode($body);
        $client->setRawBody($body);
        $response = $client->send();
        if($response->isSuccess()){
            $decodedResponse = json_decode($response->getBody(), true);
            return $decodedResponse;
        }
        else
            return $response;

    }
}

