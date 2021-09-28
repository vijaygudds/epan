<?php

namespace Omnipay\CCAvenue\Message;
use Omnipay\Common\Exception\InvalidRequestException;
include('Crypto.php');

/**
 * CCAvenue Purchase Request
 */
class CompletePurchaseRequest extends AuthorizeRequest
{
  public function getData()
  {
    $data = $this->httpRequest->request->all();
    $workingKey=$this->getSecretKey();   //Working Key should be provided here.
    $encResponse=$data["encResp"];     //This is the response sent by the CCAvenue Server
    $rcvdString=decrypt($encResponse,$workingKey);    //Crypto Decryption used as per the specified working key.
    
    $order_status="";
    $decryptValues=explode('&', $rcvdString);
    $dataSize=sizeof($decryptValues);
    $response_data = [];
    for($i = 0; $i < $dataSize; $i++) {
      $information=explode('=',$decryptValues[$i]);
      $response_data[$information[0]] = $information[1];

      if($i==3) $order_status=$information[1];
    }
    
    if(!in_array($order_status, ['Success','Aborted','Failure'])) {
        throw new InvalidRequestException('signature mismatch');
    }
    
    return $response_data;
  }

  public function sendData($data)
  {
    return $this->response = new CompletePurchaseResponse($this, $data);
  }
}
