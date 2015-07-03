<?php

require_once 'wordmailmerge.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function wordmailmerge_civicrm_config(&$config) {
  _wordmailmerge_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function wordmailmerge_civicrm_xmlMenu(&$files) {
  _wordmailmerge_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function wordmailmerge_civicrm_install() {
  require_once "CRM/Core/DAO.php";
    
    CRM_Core_DAO::executeQuery("
        CREATE TABLE IF NOT EXISTS `veda_civicrm_wordmailmerge` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `msg_template_id` int(10) NOT NULL,
          `file_id` int(10) NOT NULL COMMENT 'FK to file_civicrm',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
    ");
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function wordmailmerge_civicrm_uninstall() {
  return _wordmailmerge_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function wordmailmerge_civicrm_enable() {
  return _wordmailmerge_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function wordmailmerge_civicrm_disable() {
  return _wordmailmerge_civix_civicrm_disable();
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
function wordmailmerge_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _wordmailmerge_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function wordmailmerge_civicrm_managed(&$entities) {
  return _wordmailmerge_civix_civicrm_managed($entities);
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
function wordmailmerge_civicrm_caseTypes(&$caseTypes) {
  _wordmailmerge_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function wordmailmerge_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _wordmailmerge_civix_civicrm_alterSettingsFolders($metaDataFolders);
}


require_once 'CRM/Contact/Task.php';

function wordmailmerge_civicrm_searchTasks( $objectName, &$tasks ){
  $addArray = array(
          'title' => ts('Word Mail Merge'),
          'class' => 'CRM_Wordmailmerge_Form_WordMailMergeForm',
          'result' => TRUE,
        );
  array_push($tasks, $addArray);
}

function wordmailmerge_civicrm_buildForm( $formName, &$form ){
require_once 'CRM/Core/DAO/MessageTemplate.php';
require_once 'CRM/Core/BAO/File.php';
require_once 'CRM/Core/DAO.php';
  if($formName == 'CRM_Admin_Form_MessageTemplates'){
    $action = $form->getVar('_action');
    $template = CRM_Core_Smarty::singleton();
    $form->assign('action', $action);
    $templatePath = realpath(dirname(__FILE__)."/templates");
    $config = CRM_Core_Config::singleton();
    if( $_GET['action'] == 'update'  ){
      $msgTemplateId = $_GET['id'];
      $sql = "SELECT * FROM veda_civicrm_wordmailmerge WHERE msg_template_id = %1";
      $params = array(1 => array($msgTemplateId, 'Integer'));
      $dao = CRM_Core_DAO::executeQuery($sql, $params);
      while ($dao->fetch()) {
        $fileId = $dao->file_id ; 
      }
      if (!empty($fileId)){
        $mysql = "SELECT * FROM civicrm_file WHERE id = %1";
        $params = array(1 => array($fileId, 'Integer'));
        $dao = CRM_Core_DAO::executeQuery($mysql, $params);
        while ($dao->fetch()) {
          $default['fileID']        = $dao->id;
          $default['mime_type']     = $dao->mime_type;
          $default['fileName']      = $dao->uri;
          $default['cleanName']     = CRM_Utils_File::cleanFileName($dao->uri);
          $default['fullPath']      = $config->customFileUploadDir . DIRECTORY_SEPARATOR . $dao->uri;
          $default['url']           = CRM_Utils_System::url('civicrm/file', "reset=1&id={$dao->id}&eid={$msgTemplateId}");
          $default['href']          = "<a href=\"{$default['url']}\">{$default['cleanName']}</a>";
          $default['tag']           = CRM_Core_BAO_EntityTag::getTag($dao->id, 'civicrm_file');
          $default['deleteURLArgs'] = CRM_Core_BAO_File::deleteURLArgs('civicrm_file', $msgTemplateId, $dao->id);
        }
        $defaults[$dao->id] = $default;
        $form->assign('defaults',$defaults);
      }
    }
    if( $_GET['action'] == 'delete '){
      $msgTemplateId = $_GET['id'];
      $sql = "SELECT * FROM veda_civicrm_wordmailmerge WHERE msg_template_id = %1";
      $params = array(1 => array($msgTemplateId, 'Integer'));
      $dao = CRM_Core_DAO::executeQuery($sql, $params);
      while ($dao->fetch()) {
        $fileId = $dao->id ; 
      }
      if(empty($fileId)){
        CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1'));
        CRM_Core_Session::setStatus(ts("No attach doc in your template."));
      }else{
        $sql = "DELETE * FROM veda_civicrm_wordmailmerge WHERE msg_template_id = %1";
        $params = array(1 => array($fileId, 'Integer'));
        CRM_Core_DAO::executeQuery($sql, $params);
        CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1'));
      }
    }
    CRM_Core_BAO_File::buildAttachment( $form, 'civicrm_msg_template', '', 1 );
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1'));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "{$templatePath}/CRM/Wordmailmerge/testfield.tpl"
    ));
  }
}

function wordmailmerge_civicrm_post( $op, $objectName, $objectId, &$objectRef ){
  if( $objectName == 'MessageTemplate'){
    $config = CRM_Core_Config::singleton();
    $uploaddir = $config->customFileUploadDir;
    $value = $_FILES['attachFile_1'];
    $fileFormat = (explode(".",$value['name']));
    if($fileFormat[1] == 'docx' || $fileFormat[1] == 'doc' && !empty($fileFormat[0])){
      $newName = CRM_Utils_File::makeFileName($value['name']);
      $mime_type = $_FILES['attachFile_1']['type'];
      $uploadfile = $uploaddir.$newName;
      if (move_uploaded_file($_FILES['attachFile_1']['tmp_name'], $uploadfile)) {
        $sql = "INSERT INTO `civicrm_file` ( mime_type, uri )
                VALUES ( %1, %2 )";
        $params = array(1 => array($mime_type, 'String'), 2 => array($newName, 'String'));
        CRM_Core_DAO::executeQuery($sql, $params);
        $query = " SELECT * FROM `civicrm_file` WHERE `uri` = %1";
        $params = array(1 => array($newName, 'String'));
        $dao = CRM_Core_DAO::executeQuery($query, $params);
        while ($dao->fetch()) {
          $msgId = $dao->id ; 
        }
        $mysql = "INSERT INTO `veda_civicrm_wordmailmerge` ( msg_template_id, file_id )
                VALUES ( %1, %2 )";
        $params = array(1 => array($objectId, 'Integer'), 2 => array($msgId, 'Integer'));
        CRM_Core_DAO::executeQuery($mysql, $params);
      } else {
        $mysql = "DELETE FROM `veda_civicrm_wordmailmerge` WHERE msg_template_id = %1";
        $params = array(1 => array($objectId, 'Integer'));
        CRM_Core_DAO::executeQuery($mysql, $params);
        CRM_Core_Session::setStatus(ts("No attach doc in your new template."));
        CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1'));
      }
    }else{
      if($op == 'create' && !empty($fileFormat[0])){
        CRM_Core_Session::setStatus(ts("Attachment file is not doc format."));
        CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/messageTemplates/add", "action=add&reset=1"));
      }
      if($op == 'edit' && !empty($fileFormat[0])){
        CRM_Core_Session::setStatus(ts("Attachment file is not doc format."));
        CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/messageTemplates/add", "action=update&id=$objectId&reset=1"));
      }
    }
  }
}

function wordmailmerge_civicrm_tokens( &$tokens ) {
  
  $tokens['contact']['contact.qrcode'] =  ts("QR Code");
}

function wordmailmerge_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  if ((array_key_exists('contact', $tokens))) {
    $config = CRM_Core_Config::singleton();

    $imageUploadDir = $config->imageUploadDir;
    $extensionDir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
    $qrlibFile    = $extensionDir .'/lib/phpqrcode/phpqrcode.php';
    foreach($cids as $id){
      $filename = 'qrcode_'.$id.date('dmy').'.png';
      $pngAbsoluteFilePath = $imageUploadDir.$filename;
      $arguments = array(
        'cid'=> $id,
        'mid'=> CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $id, 'external_identifier', 'id'),
        'reset'=> 1,
        'cs' => CRM_Contact_BAO_Contact_Utils::generateChecksum($id),
      );
      $url  = CRM_Utils_System::url('civicrm/process/wordmailmerge/qrcode', $arguments, TRUE);
      //delete if the filename exists
      if (file_exists($pngAbsoluteFilePath)) { 
        unlink($pngAbsoluteFilePath);
      }
      require_once $qrlibFile;
      QRcode::png($url, $pngAbsoluteFilePath, 'L', 4, 2);
      
      // set img tag for testing.. needo to change, to get the Qrcode in Word File.
      $values[$id]['contact.qrcode'] = realpath($pngAbsoluteFilePath);
      
    }//end foreach
  }//end if
}
