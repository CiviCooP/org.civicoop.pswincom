<?php

class org_civicoop_pswincom extends CRM_SMS_Provider {

  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = array();
  
  /**
   * The default url to the pswincom api
   * 
   * @var String
   */
  public $_apiURL = "https://secure.pswin.com/XMLHttpWrapper/process.aspx";
  
  /**
   * The default type for the API 
   * Only xml is supported
   * 
   * @var String
   */
  protected $_apiType = 'xml';
  
  /**
   * Array with the provider info
   * such as the url, api type, the username and password etc.
   * 
   * @var array 
   */
  protected $_providerInfo = array();
    

  function __construct($provider = array(), $skipAuth = TRUE) {
    $apiTypeId = CRM_Utils_Array::value('api_type', $provider, false);
    if ($apiTypeId) {
      $api_group_id = civicrm_api3('OptionGroup', 'getvalue', array('name' => 'sms_api_type', 'return' => 'id'));
      $val = civicrm_api3('OptionValue', 'getvalue', array('option_group_id' => $api_group_id, 'value' => $apiTypeId, 'return' => 'name'));
      if ($val) {
        $this->_apiType = $val;
      }
    }
    $this->_apiURL = CRM_Utils_Array::value('api_url', $provider, $this->_apiURL);
    $this->_providerInfo = $provider;
  }

  /**
   * singleton function used to manage this object
   *
   * @return object
   * @static
   *
   */
  static function &singleton($providerParams = array(), $force = FALSE) {
    $providerID = CRM_Utils_Array::value('provider_id', $providerParams);
    $skipAuth = $providerID ? FALSE : TRUE;
    $cacheKey = (int) $providerID;

    if (!isset(self::$_singleton[$cacheKey]) || $force) {
      $provider = array();
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }
      self::$_singleton[$cacheKey] = new org_civicoop_pswincom($provider, $skipAuth);
    }
    return self::$_singleton[$cacheKey];
  }

  /**
   * Send an SMS Message via the API Server
   *
   * @access public
   */
  function send($recipients, $header, $message, $dncID = NULL) {    
    $session = CRM_Core_Session::singleton();    
    if ($this->_apiType == 'xml') {
      // Writing XML Document
      $xml[] = "<?xml version=\"1.0\"?>";
      $xml[] = "<!DOCTYPE SESSION SYSTEM \"pswincom_submit.dtd\">";
      $xml[] = "<SESSION>";
      $xml[] = "<CLIENT>".$this->_providerInfo['username']."</CLIENT>";
      $xml[] = "<PW>".$this->_providerInfo['password']."</PW>";
      //$xml[] = "<SD>gw2xmlhttpspost</SD>";
      $xml[] = "<MSGLST>";
      
      $receivers = explode(",", $header['to']);
      $sendTo = array();
      $id = 0;
      foreach($receivers as $receiver) {
        $id ++;
        $sendTo[$id] = $receiver;
        list($cid, $phone)  = explode("::", $receiver); //split x::yyyyyy where x = civi id and yyyyy is phone number
        $intPhone = $this->includeCountryCode($phone, $cid);
               
        $xml[] = "<MSG>";
        $xml[] = "<ID>".$id."</ID>";
        $xml[] = "<TEXT>".$message."</TEXT>";
        $xml[] = "<RCV>".$intPhone."</RCV>";
        if (array_key_exists('from', $this->_providerInfo['api_params'])) {
          $xml[] = "<SND>".$this->_providerInfo['api_params']['from']."</SND>";
        }
        $xml[] = "</MSG>";
      }
      

      $xml[] = "</MSGLST>";
      $xml[] = "</SESSION>";
      $xmldocument = utf8_decode(join("\r\n", $xml) . "\r\n\r\n");
      
      //open connection
      $ch = curl_init();
      
      //set the url, number of POST vars, POST data
      curl_setopt($ch,CURLOPT_URL, $this->_apiURL);
      curl_setopt($ch,CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/xml',                                                                                
        'Content-Length: ' . strlen($xmldocument))                                                                       
      );
      curl_setopt($ch,CURLOPT_POSTFIELDS, $xmldocument);
      
      // receive server response ...
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      //execute post
      $response = curl_exec($ch);
      
      $error = false;
      if (curl_errno($ch)) {
        $error = curl_error($ch);
      }
      
      //close connection
      curl_close($ch);
      
      if ($error !== false) {
        $session->setStatus(ts('Sending SMS Failed: %1', $error), '', 'error');
        return false;
      }
      
      $xmlResponse = new SimpleXMLElement(trim($response));
      foreach($xmlResponse->MSGLST->children() as $msg) {
        if ( (string) $msg->STATUS != 'OK') {
          //error for contact
          $receiver = $sendTo[(string) $msg->ID];
          list($cid, $phone)  = explode("::", $receiver);
          $to = CRM_Contact_BAO_Contact::displayName($cid) . ' &lt;'.$phone.'&gt;';
          $session->setStatus(ts("Failed to send message to '%1' because '%2'", $to, (string) $msg->INFO));
        }
      }
      return true;
    }
    return false;
  }

  function includeCountryCode($phone, $cid) {
    if (stripos("+", $phone) === 0) {
      return $phone;
    } elseif (stripos("00", $phone)===0) {
      return $phone;
    } 
    
    //assume contact lifes in norway
    return "+47".$phone;//norwegian phone number;
  }
  
  function inbound() {
    $xml[] = "<?xml version=\"1.0\"?>";
    $xml[] = "<!DOCTYPE MSGLST SYSTEM \"pswincom_receive_response.dtd\">";
    $xml[] = "<MSGLST>";
    
    $content = file_get_contents('php://input', 'r');
    
    CRM_Core_Error::debug_log_message('Received SMS with contents: '.$content);
    
    $xmlRequest = new SimpleXMLElement(trim($content));
    foreach($xmlRequest->MSGLST->children() as $msg) {
      $from = (string) $msg->SND;
      $body = (string) $msg->TEXT;
      $to = (string) $msg->RCV;
      
      parent::processInbound($from, $body, $to);
      
      $xml[] = "<MSG>";
      $xml[] = "<ID>".(string) $msg->ID . "</ID>";
      $xml[] = "<STATUS>OK</STATUS>";
      $xml[] = "</MSG>";
    }
    
    $xml[] = "</MSGLST>";
    $xmldocument = utf8_decode(join("\r\n", $xml) . "\r\n\r\n");
    echo $xmldocument;
    
    CRM_Utils_System::civiExit();
    
  }
}
