<?php

class CRM_Pswincom_SMSProvider extends CRM_SMS_Provider {
  
  function __construct($provider = array(), $skipAuth = TRUE) {
    
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
    $skipAuth   = $providerID ? FALSE : TRUE;
    $cacheKey   = (int) $providerID;

    if (!isset(self::$_singleton[$cacheKey]) || $force) {
      $provider = array();
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }
      self::$_singleton[$cacheKey] = new CRM_Pswincom_SMSProvider($provider, $skipAuth);
    }
    return self::$_singleton[$cacheKey];
  }
  
  /**
   * Send an SMS Message via the API Server
   *
   * @access public
   */
  function send($recipients, $header, $message, $dncID = NULL) {
    
  }
  
}

