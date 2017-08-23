<?php

require_once 'CRM/Core/Page.php';

class CRM_Wordmailmerge_Utils {

  /*
   * Get CiviCRM version using SQL
   * Using function to get version is not compatible with all versions
   */
  public static function getCiviVersion() {
    $sql = "SELECT version FROM civicrm_domain";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    return $dao->version;
  }

  /*
   *Function to check if a template is marked as wordmailmerge template
   */
  public static function isWordmailmergeTemplate($templateId = '') {
    $isWordmailmergeTemplate = FALSE;

    if (empty($templateId)) {
      return $isWordmailmergeTemplate;
    }

    // check if template id is in wordmailmerge table
    $sql = "SELECT * FROM `veda_civicrm_wordmailmerge` WHERE msg_template_id = %1";
    $params = array(
      1 => array($templateId , 'Integer')
    );
    $dao = CRM_Core_DAO::executeQuery($sql, $params);

    if ($dao->fetch()) {
      $isWordmailmergeTemplate = TRUE;
    }
    return $isWordmailmergeTemplate;
  }

  /*
   *Function to get attached file id of a message template
   */
  public static function getAttachedFileId($templateId = '') {
    $fileId = '';

    if (empty($templateId)) {
      return $fileId;
    }

    $sql = "SELECT * FROM civicrm_entity_file WHERE entity_id = %1";
    $params = array(
      1 => array($templateId , 'Integer')
    );
    $dao = CRM_Core_DAO::executeQuery($sql, $params);

    while ($dao->fetch()) {
      $fileId = $dao->file_id;
    }

    return $fileId;
  }

  /*
  Function to record wordmailmerge as activity
  */
  static function recordActivity($contactIds = array()) {
    if (empty($contactIds)) {
      CRM_Core_Error::debug_var('no contact records to create wordmailmerge activity ', $contactIds);
      return;
    }

    // set logged in user as activity source contact
    $session = CRM_Core_Session::singleton();
    $sourceContactId = $session->get('userID');

    // Wordmailmerge activityType name
    $aTypeName = CRM_Wordmailmerge_Constants::C_ACT_TYPE_NAME_WORDMAILMERGE;

    // get activityTypeId from name
    $activityTypes = CRM_Core_PseudoConstant::activityType(TRUE, FALSE, FALSE, 'name');
    $typeId = array_search($aTypeName, $activityTypes);

    $activityParams = array(
      'source_contact_id'  => $sourceContactId,
      'subject'           => 'Wordmailmerge Letter printed',
      'status_id'         => 'Completed',
      'activity_type_id'  => $typeId
    );

    // Create activity for each contact record
    foreach ($contactIds as $key => $contactId) {
      $activityParams['target_contact_id'] = $contactId;

      $activityResult = self::civicrm_api_wrapper('Activity', 'create', $activityParams);
      if (!isset($activityResult)) {
        $errorMsg = 'Activity not created for '.$contactId.'. Please contact admin!';
        CRM_Core_Session::setStatus(ts($errorMsg), ts('Word Mail Merge'), 'error');
      }
    }
  }

  /**
    * CiviCRM API wrapper
    *
    * @param string $entity
    * @param string $action
    * @param array $params
    *
    * @return array of API results
  */
  static function civicrm_api_wrapper($entity, $action, $params) {

    if (empty($entity) || empty($action) || empty($params)) {
      return;
    }

    try {
      $result = civicrm_api3($entity, $action, $params);
    }
    catch (Exception $e) {
      CRM_Core_Error::debug_var('CiviCRM API Call Error', $e);
      return;
    }

    return $result;
  }

}
