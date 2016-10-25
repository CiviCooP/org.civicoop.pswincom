<?php

/**
 * Collection of upgrade steps
 */
class CRM_Pswincom_Upgrader extends CRM_Pswincom_Upgrader_Base {

  public function install() {
    $groupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'sms_provider_name', 'id', 'name');
    $params = array(
      'option_group_id' => $groupID,
      'label' => 'PSWinCom',
      'value' => 'org.civicoop.pswincom',
      'name' => 'PSWinCom',
      'is_default' => 1,
      'is_active' => 1,
      'version' => 3,
    );
    civicrm_api3('OptionValue', 'create', $params);

    $this->executeSqlFile('sql/install.sql');
  }

  public function upgrade_1001() {
    $this->executeSqlFile('sql/install.sql');
    return true;
  }

  public function uninstall() {
    $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'PSWinCom', 'id', 'name');
    if ($optionID) {
      CRM_Core_BAO_OptionValue::del($optionID);
    }
    $filter = array('name' => 'org.civicoop.pswincom');
    $Providers = CRM_SMS_BAO_Provider::getProviders(False, $filter, False);
    if ($Providers) {
      foreach ($Providers as $key => $value) {
        CRM_SMS_BAO_Provider::del($value['id']);
      }
    }
  }

  public function enable() {
    $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'PSWinCom', 'id', 'name');
    if ($optionID) {
      CRM_Core_BAO_OptionValue::setIsActive($optionID, TRUE);
    }
    $filter = array('name' => 'org.civicoop.pswincom');
    $Providers = CRM_SMS_BAO_Provider::getProviders(False, $filter, False);
    if ($Providers) {
      foreach ($Providers as $key => $value) {
        CRM_SMS_BAO_Provider::setIsActive($value['id'], TRUE);
      }
    }
  }

  public function disable() {
    $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'PSWinCom', 'id', 'name');
    if ($optionID) {
      CRM_Core_BAO_OptionValue::setIsActive($optionID, FALSE);
    }

    $filter = array('name' => 'org.civicoop.pswincom');
    $Providers = CRM_SMS_BAO_Provider::getProviders(False, $filter, False);
    if ($Providers) {
      foreach ($Providers as $key => $value) {
        CRM_SMS_BAO_Provider::setIsActive($value['id'], FALSE);
      }
    }
  }

}
