<?php

namespace Omnipay\CCAvenue\Message;
include('Crypto.php');
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
//For HTTP Response
use Omnipay\Common\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Stripe Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {  
        $this->request = $request;
        $this->data = $data;
        $this->redirectUrl = $redirectUrl;
        // $this->redirectUrl = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    }

    public function isSuccessful()
    {
        return FALSE;
    }

    public function redirect(){  
        $this->getRedirectResponse()->send();
        exit;
    }

    public function getRedirectResponse(){

        if (!$this instanceof RedirectResponseInterface || !$this->isRedirect()) {
            throw new RuntimeException('This response does not support redirection.');
        }

        if ('GET' === $this->getRedirectMethod()) {
            return HttpRedirectResponse::create($this->getRedirectUrl());
        } elseif ('POST' === $this->getRedirectMethod()) {
            $hiddenFields = '';
            $merchant_data='';
            $working_key=$this->data['working_key'];//Shared by CCAVENUES
            $access_code=$this->data['access_key'];//Shared by CCAVENUES
            foreach ($this->getRedirectData() as $key => $value){
                // echo $key."= ".$value.'<br/>';
                $merchant_data.=$key.'='.urlencode($value).'&';
            }

            $encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.


            // foreach ($this->getRedirectData() as $key => $value) {
            //     $hiddenFields .= sprintf(
            //         '<input type="hidden" name="%1$s" value="%2$s" />',
            //         htmlentities($key, ENT_QUOTES, 'UTF-8', false),
            //         htmlentities($value, ENT_QUOTES, 'UTF-8', false)
            //     )."\n";
            // }

            $output = '<!DOCTYPE html>
                    <html>
                        <head>
                            <title>Redirecting...</title>
                        </head>
                        <body onload="document.forms[0].submit();">
                            <form action="%1$s" method="post" style="display:none;">
                                <p>Redirecting to payment page...</p>
                                <input type=text name=encRequest value="%2$s"/>
                                <input type=text name=access_code value="%3$s"/>
                                <p>
                                    <input type="submit" value="Continue" />
                                </p>
                            </form>
                        </body>
                    </html>';   

                $output = sprintf(
                    $output,
                    htmlentities($this->redirectUrl, ENT_QUOTES, 'UTF-8', false),
                    $encrypted_data,
                    $access_code
                );

                return HttpResponse::create($output);
            }

            throw new RuntimeException('Invalid redirect method "'.$this->getRedirectMethod().'".');
    }

    public function isRedirect()
    {
        return TRUE;
    }

    public function isTransparentRedirect()
    {
      return TRUE;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }

    public function getRedirectResponseHiddenFields() {
      $hiddenFields = '';
      foreach ($this->getRedirectData() as $key => $value) {
        $hiddenFields .= sprintf(
            '<input type="hidden" name="%1$s" value="%2$s" />',
            htmlentities($key, ENT_QUOTES, 'UTF-8', false),
            htmlentities($value, ENT_QUOTES, 'UTF-8', false)
          )."\n";
      }
      return $hiddenFields;
    }
}
