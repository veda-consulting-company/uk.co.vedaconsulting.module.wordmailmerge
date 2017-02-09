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

}
