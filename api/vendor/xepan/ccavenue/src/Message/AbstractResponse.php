<?php

namespace Omnipay\CCAvenue\Message;

use Omnipay\Common\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Abstract Response
 */
abstract class AbstractResponse implements ResponseInterface
{
    protected $request;
    protected $data;

    public function __construct(RequestInterface $request, $data)
    {   
        $this->request = $request;
        $this->data = $data;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function isRedirect()
    {           
        return false;
    }

    public function isTransparentRedirect()
    {
        return false;
    }

    public function isCancelled()
    {
        return false;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMessage()
    {
        return null;
    }

    public function getCode()
    {
        return null;
    }

    public function getTransactionReference()
    {
        return null;
    }

    /**
     * Automatically perform any required redirect
     *
     * This method is meant to be a helper for simple scenarios. If you want to customize the
     * redirection page, just call the getRedirectUrl() and getRedirectData() methods directly.
     *
     * @codeCoverageIgnore
     */
    
}
