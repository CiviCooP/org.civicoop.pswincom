<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Pswincom.ProcessInbound API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_pswincom_processinbound($params) {
  $limit = 100;
  if (isset($params['limit']) && is_numeric($params['limit'])) {
    $limit = $params['limit'];
  }

  $ids = array();
  $dao = CRM_Core_DAO::executeQuery("SELECT * FROM `civicrm_pswincom_inbound` ORDER BY `date` ASC LIMIT %1", array(1=>array($limit, 'Integer')));
  while ($dao->fetch()) {
    $provider = CRM_SMS_Provider::singleton(array(
      'provider_id' => $dao->provider_id,
    ));
    $provider->processInbound($dao->from, $dao->body, $dao->to);
    $ids[] = $dao->id;
  }

  if (count($ids)) {
    CRM_Core_DAO::executeQuery("DELETE FROM `civicrm_pswincom_inbound` WHERE `id` IN (".implode(", ", $ids).");");
  }

  return civicrm_api3_create_success();
}