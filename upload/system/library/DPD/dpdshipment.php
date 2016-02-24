<?php

class DpdShipment
{
  /**
   * Path to ParcelShopFinder webservice wsdl.
   */
  CONST WEBSERVICE_SHIPMENT = 'ShipmentService.svc?wsdl';
  
  public $login;
  public $request;
  
  public $result;
  
  public function __construct(DpdLogin $login)  
  {
    $this->login = $login;
  }
  
  public function send()
  {
    if(!isset($request['printOptions']))
    {
      $this->request['printOptions']['printerLanguage'] = 'PDF';
      $this->request['printOptions']['paperFormat'] = 'A6';
    }
    
    $counter = 0;
    $stop = false;
    while(!$stop
      && $counter < 3)
    {
      try {
        $client = new SoapClient($this->getWebserviceUrl($this->login->url), array('trace' => 1));
        
        $soapHeader = $this->login->getSoapHeader();
        $client->__setSoapHeaders($soapHeader);
        
        $startTime = microtime(true);
        $result = $client->storeOrders($this->request);
        $endTime = microtime(true);
        
        if($this->login->timeLogging)
          $this->logTime($endTime - $startTime);
      } 
      catch (SoapFault $soapE) 
      {
        switch($soapE->getCode())
        {
          case 'soap:Server':
            $splitMessage = explode(':', $soapE->getMessage());
            switch($splitMessage[0])
            {
              case 'cvc-complex-type.2.4.a':
                $newMessage = 'One of the mandatory fields is missing.';
                break;
              case 'cvc-minLength-valid':
                $newMessage = 'One of the values you provided is not long enough.';
                break;
              case 'cvc-maxLength-valid':
                $newMessage = 'One of the values you provided is too long.';
                break;
              case 'Fault occured':
                if(isset($soapE->detail) && isset($soapE->detail->authenticationFault))
                {
                  $counter++;
                  if($counter < 3)
                  {  
                    switch($soapE->detail->authenticationFault->errorCode)
                    {
                      case 'LOGIN_7':
                        $this->login->refresh();
                        continue 4;
                        break;
                      default:
                        $newMessage = $soapE->detail->authenticationFault->errorMessage;
                        break;
                    }
                  } 
                  else
                    $newMessage = 'Maximum retries exceeded: ' . $soapE->detail->authenticationFault->errorMessage;
                }
                else
                  $newMessage = 'Something went wrong, please use the Exception trace to find out. ' . serialize($soapE->detail);
                  //TODO: add shipment errors here!
                break;
              default:
                $newMessage = $soapE->getMessage() . $client->__getLastRequest();
                break;
            }
            break;
          case 'soap:Client':
            switch($soapE->getMessage())
            {
              case 'Error reading XMLStreamReader.':
                $newMessage = 'It looks like their is a typo in the xml call.';
                break;
              default:
                $newMessage = $soapE->getMessage();
                break;
            }
            break;
          default:
            $newMessage = $soapE->getMessage() . $client->__getLastRequest();
            break;
        }
        throw new Exception($newMessage, $soapE->getCode(), $soapE);
      } 
      catch (Exception $e) 
      {
        throw new Exception('Something went wrong with the connection to the DPD server', $e->getCode(), $e);
      }
      $stop = true;
    }
    
    if(isset($result->orderResult->shipmentResponses->faults))
    {
    
      $fault = $result->orderResult->shipmentResponses->faults;
      $message = $fault->message .' ('. $fault->faultCode . ')';
      
      throw new Exception($message);
    }
    
    $this->result = $result;
    return $result;
  }
  
  /**
  * Add trailing slash to url if not exists.
  *
  * @param $url
  * @return mixed|string
  */
  protected function getWebserviceUrl($url)
  {
      if (substr($url, -1) != '/') {
          $url = $url . '/';
      }

      return $url . self::WEBSERVICE_SHIPMENT;
  }
  
  private function logTime($time)
  {
    $params['entry.1319880751'] = $this->login->url;
    $params['entry.2100714811'] = self::WEBSERVICE_SHIPMENT;
    $params['entry.667346972'] = str_replace('.',',',$time);
    $params['submit'] = "Verzenden";
    
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.$val;
    }
    $post_string = implode('&', $post_params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://docs.google.com/forms/d/1FZqWVldCn4QvIP1NJU1zgYgJRJrTIwWThwIViLhkvBs/formResponse"); //"http://localhost/googletest.php"); //
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    $result = curl_exec($ch);
    curl_close($ch);
  }
}