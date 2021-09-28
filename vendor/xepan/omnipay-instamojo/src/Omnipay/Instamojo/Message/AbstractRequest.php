<?php

namespace Omnipay\Instamojo\Message;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * Class AbstractRequest
 * @package Omnipay\Instamojo\Message
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    protected $liveEndpoint = 'https://www.instamojo.com/api/1.1/';
    /**
     * @var string
     */
    protected $testEndpoint = 'https://test.instamojo.com/api/1.1/';

    /**
     * @param $method
     * @param $endpoint
     * @param $data
     * @return RequestInterface
     */
    public function createRequest($method, $endpoint, $data = null)
    {
        return $this->httpClient->createRequest($method, $endpoint, null, $data);
    }

    /**
     * @param RequestInterface $httpRequest
     * @return array|bool|float|int|string
     */
    public function sendRequest(RequestInterface $httpRequest)
    {
        $httpRequest
            ->setHeader('X-Api-key', $this->getApiKey())
            ->setHeader('X-Auth-Token', $this->getAuthToken());

        try {
            $httpResponse = $httpRequest->send();
        } catch (ClientErrorResponseException $e) {
            $httpResponse = $e->getResponse();
        }

        return $httpResponse->json();
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->getParameter('salt');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setSalt($value)
    {
        return $this->setParameter('salt', $value);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('api_key');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setApiKey($value)
    {
        return $this->setParameter('api_key', $value);
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->getParameter('auth_token');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setAuthToken($value)
    {
        return $this->setParameter('auth_token', $value);
    }

}
