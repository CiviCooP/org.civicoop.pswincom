<?php

require_once 'pswincom.civix.php';

const PSWINCON_MAX_SMS_LENGT = 804;

function pswincom_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_SMS_Form_Upload' || $formName == 'CRM_Contact_Form_Task_SMS' || $formName == 'CRM_Smsautoreply_Form_Autoreplies') {
    $form->assign('max_sms_length',PSWINCON_MAX_SMS_LENGT);
  }
  
}

function pswincom_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
  if ($formName == 'CRM_SMS_Form_Upload' || $formName == 'CRM_Contact_Form_Task_SMS' || $formName == 'CRM_Smsautoreply_Form_Autoreplies') {
    if ($formName == 'CRM_SMS_Form_Upload') {
      unset($errors['textFile']);
      $form->setElementError('textFile', NULL);
    }
    unset($errors['text_message']);
    $form->setElementError('text_message', NULL);
    if (CRM_Utils_Array::value('text_message', $fields)) {
      $messageCheck = CRM_Utils_Array::value('text_message', $fields);
      $messageCheck = str_replace("\r\n", "\n", $messageCheck);
      if ($messageCheck && (strlen($messageCheck) > PSWINCON_MAX_SMS_LENGT)) {
        $errors['text_message'] = ts("You can configure the SMS message body up to %1 characters", array(1 => PSWINCON_MAX_SMS_LENGT));
      }
    }
  }
  
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function pswincom_civicrm_config(&$config) {
  _pswincom_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function pswincom_civicrm_xmlMenu(&$files) {
  _pswincom_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function pswincom_civicrm_install() {
  return _pswincom_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function pswincom_civicrm_uninstall() {
  return _pswincom_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function pswincom_civicrm_enable() {
  return _pswincom_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function pswincom_civicrm_disable() {
  return _pswincom_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function pswincom_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _pswincom_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function pswincom_civicrm_managed(&$entities) {
  return _pswincom_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function pswincom_civicrm_caseTypes(&$caseTypes) {
  _pswincom_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function pswincom_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _pswincom_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
