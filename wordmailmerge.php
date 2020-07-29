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
          `mailmerge_option_id` int(10) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
    ");

    CRM_Core_DAO::executeQuery("
        CREATE TABLE IF NOT EXISTS `veda_civicrm_excelmailmerge` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `template_title` VARCHAR(255) NOT NULL,
          `is_active` tinyint(4) DEFAULT '1',
          `file_id` int(10) NOT NULL COMMENT 'FK to file_civicrm',
          `mailmerge_option_id` int(10) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
    ");

    // import auto_install.xml file
    $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
    $extXMLFile = $extRoot . DIRECTORY_SEPARATOR . 'xml/auto_install.xml';
    require_once 'CRM/Utils/Migrate/Import.php';
    $import = new CRM_Utils_Migrate_Import( );
    $import->run( $extXMLFile );
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Add entries to the navigation menu, automatically removed on uninstall
 */
function wordmailmerge_civicrm_navigationMenu(&$menu) {
  $mailMergeTemplate = [
    'name' => 'Mail Merge',
    'url' => 'civicrm/MailMergeTemplate',
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _wordmailmerge_civix_insert_navigation_menu($menu, 'Administer/CiviContribute', $mailMergeTemplate);
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
  if ($objectName == 'contact' || $objectName == 'membership') {
    $taskExist = FALSE;
    foreach ($tasks as $key => $value) {
      if (($value['class'] == 'CRM_Wordmailmerge_Form_WordMailMergeForm') || ($value['class'] == 'CRM_Wordmailmerge_Form_LibreOfficeMailMergeForm')) {
        $taskExist = TRUE;
      }
    }
  
    if (!$taskExist) {
      $addArray = array(
        'title' => ts('Word Mail Merge '),
        'class' => 'CRM_Wordmailmerge_Form_WordMailMergeForm',
        'result' => TRUE,
      );
      $addArray2 = array(
        'title' => ts('Libreoffice Mail Merge '),
        'class' => 'CRM_Wordmailmerge_Form_LibreOfficeMailMergeForm',
        'result' => TRUE,
      );
      array_push($tasks, $addArray, $addArray2);
    }  
  }elseif($objectName == 'contribution'){
    $taskExist = FALSE;
    foreach($tasks as $key => $value){
      if(($value['class'] == 'CRM_Wordmailmerge_Form_LibreOfficeCalcMailMergeForm') || ($value['class'] == 'CRM_Wordmailmerge_Form_LibreOfficeMailMergeForm')){
        $taskExist = TRUE;
      }
    }
    if(!$taskExist){
      $addArray = array (
        'title' => ts('LibreOffice Calc Mail Merge'),
        'class' => 'CRM_Wordmailmerge_Form_LibreOfficeCalcMailMergeForm',
        'result' => TRUE
      );
      $addArray2 = array(
        'title' => ts('Ms Excel Mail Merge '),
        'class' => 'CRM_Wordmailmerge_Form_MsExcelMailMergeForm',
        'result' => TRUE,
      );
      array_push($tasks, $addArray, $addArray2);
    }
  } 
}

function wordmailmerge_civicrm_buildForm( $formName, &$form ){

    if($formName == 'CRM_Admin_Form_MessageTemplates'){

      // Check CiviCRM version & use appropriate code
      $civiVersion = CRM_Wordmailmerge_Utils::getCiviVersion();

      // If CiviCRM version is less than 4.7, Use the existing code
      if ($civiVersion < 4.7) {
        require_once 'CRM/Core/DAO/MessageTemplate.php';
        require_once 'CRM/Core/BAO/File.php';
        require_once 'CRM/Core/DAO.php';

        $action = $form->getVar('_action');
        $template = CRM_Core_Smarty::singleton();
        $form->assign('action', $action);
        $templatePath = realpath(dirname(__FILE__)."/templates");
        $config = CRM_Core_Config::singleton();
        if($action == CRM_Core_Action::UPDATE){
          $defaultValues = $form->getVar('_defaultValues');
          $msgTemplateId = $defaultValues['id'];
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
              $default['deleteURLArgs'] = CRM_Core_BAO_File::deleteURLArgs('civicrm_msg_template', $msgTemplateId, $dao->id);
            }
            $defaults[$dao->id] = $default;
            $form->assign('defaults',$defaults);
          }
        }
        CRM_Core_BAO_File::buildAttachment( $form, 'civicrm_msg_template', '', 1 );
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1'));
        CRM_Core_Region::instance('page-body')->add(array(
          'template' => "{$templatePath}/CRM/Wordmailmerge/testfield.tpl"
        ));
      }
      // End of CiviCRM version earlier than 4.7

      // In CiviCRM version 4.7 and latest, 'Upload Document' to Mailing template has been added in the core. Hence, adding just a checkbox to mark a Message Template as wordmail merge template
      else {

        $templatePath = realpath(dirname(__FILE__)."/templates");
     
        $form->add('select', 'is_mail_merge', ts('Mail merge'),
          array(
            '0' => ts('- select -'),
            '1' => ts('Word template'),
            '2' => ts('LibreOffice Writer template')
          ) 
        );
        
        // add required template block
        CRM_Core_Region::instance('page-body')->add(array(
          'template' => "{$templatePath}/CRM/Wordmailmerge/customFields.tpl"
        ));

        // If Updating Message template, Check and set default values
        if ($form->getAction() == CRM_Core_Action::UPDATE) {
          $templateId = $form->getVar('_id');

          // check if the template is already wordmailmerge template
          $isWordmailmergeTemplate = CRM_Wordmailmerge_Utils::isWordmailmergeTemplate($templateId);

          // if it is already a wordmailmerge template, mark the checkbox ticked
          if ($isWordmailmergeTemplate) {
            // default values
            $defaults = array('is_wordmailmerge' => 1);
            $form->setDefaults($defaults);
          }
        }

      }
      // End of CiviCRM version 4.7 and latest

    }

}


function wordmailmerge_civicrm_post( $op, $objectName, $objectId, &$objectRef ){
  if( $objectName == 'MessageTemplate'){

    // Check CiviCRM version & use this code only for CiviCRM version less than 4.7
    $civiVersion = CRM_Wordmailmerge_Utils::getCiviVersion();

    if ($civiVersion < 4.7) {

      $config = CRM_Core_Config::singleton();
      $uploaddir = $config->customFileUploadDir;
      $value = $_FILES['attachFile_1'];
      $fileFormat = (explode(".",$value['name']));
      if(isset($fileFormat[1]) && ($fileFormat[1] == 'docx' || $fileFormat[1] == 'odt' && !empty($fileFormat[0]))){
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

          $sql = "INSERT INTO `civicrm_entity_file` ( entity_table, entity_id, file_id )
                  VALUES ( %1, %2, %3 )";
          $params = array(1 => array('civicrm_msg_template', 'String'), 2 => array($objectId, 'Integer'), 3 => array($msgId, 'Integer'));
          CRM_Core_DAO::executeQuery($sql, $params);
          $mysql = "INSERT INTO `veda_civicrm_wordmailmerge` ( msg_template_id, file_id )
                  VALUES ( %1, %2 )";
          $params = array(1 => array($objectId, 'Integer'), 2 => array($msgId, 'Integer'));
          CRM_Core_DAO::executeQuery($mysql, $params);
        }else {
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
}

// create address block token
function wordmailmerge_civicrm_tokens(&$tokens){

  $tokens['contact'] = array(
    'contact.address_block' => 'Address Block',
  );

}

// assign address fileds as a block in address_block token
function wordmailmerge_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null){

  if (!empty($tokens['contact'])) {
    if (is_null($cids) ) { $cids = array(); }
    
    foreach($cids as $id){
      $params   = array('contact_id' => $id, 'version' => 3,);
      $contact  = civicrm_api( 'Contact' , 'get' , $params );

      if(!$contact['is_error']) {
        $values[$id]['address_block'] = nl2br(CRM_Utils_Address::format($contact['values'][$id]));
        // remove blank lines
        $values[$id]['address_block'] = str_replace('<br />', "", $values[$id]['address_block']);
      }
    }
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function wordmailmerge_civicrm_postprocess($formName, &$form) {

  if ($formName == 'CRM_Admin_Form_MessageTemplates') {

    // Check CiviCRM version & return if the version is less than 4.7
    $civiVersion = CRM_Wordmailmerge_Utils::getCiviVersion();
    if ($civiVersion < 4.7) {
      return;
    }

    if (!$form->getVar('_id')) {
      CRM_Core_Error::debug_var('message templateId not received ', $form);
      return;
    }

    $templateId = $form->getVar('_id');
    //get the mail merge option
    $mailMergeOptionId = '';

    // process 'New Message template'
    if ( $form->getAction() == CRM_Core_Action::ADD ) {

      // if 'is_wordmailmerge' checkbox is checked, insert uploaded document into wordmailmerge table
      if ( isset($form->_submitValues['is_mail_merge']) && $form->_submitValues['is_mail_merge'] ) {

        foreach($form->_submitValues as $key => $value){
          if($key == 'is_mail_merge'){
            $mailMergeOptionId = $value;          
          }
        }

        // get attached fileId
        $attachedFileId = CRM_Wordmailmerge_Utils::getAttachedFileId($templateId);

        // Link uploaded document into wordmailmerge table
        if (!empty($attachedFileId)) {
          $sql = "INSERT INTO veda_civicrm_wordmailmerge ( msg_template_id, file_id, mailmerge_option_id )
                  VALUES ( %1, %2 , %3)";
          $params = array(
            1 => array($templateId , 'Integer'),
            2 => array($attachedFileId, 'Integer'),
            3 => array($mailMergeOptionId, 'Integer')
          );
          $dao = CRM_Core_DAO::executeQuery($sql, $params);
        }

      }
    }
    // End of process 'New Message template'

    // Process 'Edit message template' action
    if ( $form->getAction() == CRM_Core_Action::UPDATE ) {

      //check if the template is already linked to wordmailmerge table
      $isWordmailmergeTemplate = CRM_Wordmailmerge_Utils::isWordmailmergeTemplate($templateId);

      // get attached fileId
      $attachedFileId = CRM_Wordmailmerge_Utils::getAttachedFileId($templateId);

      // if already a wordmailmerge template and is_wordmailmerge is selected
      if ( $isWordmailmergeTemplate && isset($form->_submitValues['is_mail_merge']) && $form->_submitValues['is_mail_merge'] ) {

        // Possibility of file has been updated, so update wordmailmerge table with the file Id
        if (!empty($attachedFileId)) {
          $sql = "UPDATE veda_civicrm_wordmailmerge SET file_id = %2 WHERE  msg_template_id = %1";
          $params = array(
            1 => array($templateId , 'Integer'),
            2 => array($attachedFileId, 'Integer')
          );
          $dao = CRM_Core_DAO::executeQuery($sql, $params);
        }

      }
      // if not a wordmailmerge template and now marked as is_wordmailmerge
      elseif ( !$isWordmailmergeTemplate && isset($form->_submitValues['is_mail_merge'])) {


        foreach($form->_submitValues as $key => $value){
          if($key == 'is_mail_merge'){
            $mailMergeOptionId = $value;          
          }
        }

        // Link uploaded document into wordmailmerge table
        if (!empty($attachedFileId)) {
          $sql = "INSERT INTO veda_civicrm_wordmailmerge ( msg_template_id, file_id, mailmerge_option_id )
                  VALUES ( %1, %2, %3 )";
          $params = array(
            1 => array($templateId , 'Integer'),
            2 => array($attachedFileId, 'Integer'),
            3 => array($mailMergeOptionId, 'Integer')
          );
          $dao = CRM_Core_DAO::executeQuery($sql, $params);
        }

      }
      // if already a wordmailmerge template and is_wordmailmerge is unchecked now
      elseif ( $isWordmailmergeTemplate && !isset($form->_submitValues['is_wordmailmerge']) ) {
        // Remove the link from wordmailmerge table
        $sql = "DELETE FROM `veda_civicrm_wordmailmerge` WHERE msg_template_id = %1";
        $params = array(
          1 => array($templateId, 'Integer')
        );
        $dao = CRM_Core_DAO::executeQuery($sql, $params);

      }

    }
    // End of Process 'Edit message template' action

    // Process 'Delete message template' action
    if ( $form->getAction() == CRM_Core_Action::DELETE ) {

      // check if this is a wordmailmerge template
      $isWordmailmergeTemplate = CRM_Wordmailmerge_Utils::isWordmailmergeTemplate($templateId);

      // if this is wordmailmerge template, remove the link from wordmailmerge table
      if ($isWordmailmergeTemplate) {
        // Remove the link from wordmailmerge table
        $sql = "DELETE FROM `veda_civicrm_wordmailmerge` WHERE msg_template_id = %1";
        $params = array(
          1 => array($templateId, 'Integer')
        );
        $dao = CRM_Core_DAO::executeQuery($sql, $params);
      }

    }
    // End of Process 'Delete message template' action

  }

}

