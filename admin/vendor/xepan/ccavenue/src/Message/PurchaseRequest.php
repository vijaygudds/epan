<?php

namespace Omnipay\CCAvenue\Message;

/**
 * CCAvenue Purchase Request
 */
class PurchaseRequest extends AuthorizeRequest
{
  public function getTransactionType()
  {
    return 'sale';
  }

  public function send($param=array())
    {	  
        $data = $this->getData();
        $data = array_replace($data, $param);
        return $this->sendData($data);
    }

}
