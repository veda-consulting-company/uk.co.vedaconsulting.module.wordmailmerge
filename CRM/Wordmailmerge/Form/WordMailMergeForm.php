<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Wordmailmerge_Form_WordMailMergeForm extends CRM_Contact_Form_Task {
  CONST  TOKEN_VAR_NAME = "CiviCRM";
  static protected $_searchFormValues;
  function preProcess() {
    //get all preProcessCommon Values 
    self::preProcessCommon($this);
    $token = CRM_Core_SelectValues::contactTokens();
    
    //Membership Tokens
    $token = $token + CRM_Core_SelectValues::membershipTokens();

    $tokenMerge = array();
    foreach ($token as $key => $label) {
      $tokenMerge [] = array(
        'id' => $key,
        'text' => $label,
      );
      
    }
    
    //construct array to manage token name and label
    foreach ($tokenMerge as $tmKey => $tmValue) {
      $tokenFullName =  str_replace(array('{','}'),"",$tmValue['id']);
      $explodedTokenName =  explode('.', $tokenFullName);
      $tokenMerge[$tmKey]['token_name'] =  ($explodedTokenName[0] != 'contact') ? $tokenFullName : $explodedTokenName[1];
      if ($explodedTokenName[0] != 'civiqrcode'){
        if ($explodedTokenName[0] == 'membership') {
          $tokenMerge[$tmKey]['var_name'] =  '['.self::TOKEN_VAR_NAME.'.'.$tokenFullName.';block=w:tr]';
        }
        else {
          $tokenMerge[$tmKey]['var_name'] =  '['.self::TOKEN_VAR_NAME.'.'.$explodedTokenName[1].';block=w:tr]';
        }
      }
      else {
        $tokenMerge[$tmKey]['var_name'] =  '['.self::TOKEN_VAR_NAME.'.'.$tokenFullName.';block=w:image;ope=changepic]';
      }
      $this->_allTokens[$explodedTokenName[0]][] = $explodedTokenName[1];
      $this->_returnProperties[$explodedTokenName[1]] = 1;
    }
    
    $this->_tokenMerge = $tokenMerge;
    $this->assign('availableTokens', $this->_tokenMerge);

  }
  
  static function preProcessCommon(&$form) {
    $form->_contactIds = array();
    $form->_contactTypes = array();
    $form->_searchFrom = 'contact';
    $searchformName = $form->urlPath[1];
    if ($searchformName == 'member') {
      $form->_searchFrom = $searchformName;
      $values = $form->controller->exportValues($form->get('searchFormName'));
      $ids = array();
      if ($values['radio_ts'] == 'ts_sel') {
        foreach ($values as $name => $value) {
          if (substr($name, 0, CRM_Core_Form::CB_PREFIX_LEN) == CRM_Core_Form::CB_PREFIX) {
            $ids[] = substr($name, CRM_Core_Form::CB_PREFIX_LEN);
          }
        }
      }
      else {
        $queryParams = $form->get('queryParams');
        $sortOrder = null;
        if ( $form->get( CRM_Utils_Sort::SORT_ORDER  ) ) {
          $sortOrder = $form->get( CRM_Utils_Sort::SORT_ORDER );
        }
        $query = new CRM_Contact_BAO_Query($queryParams, NULL, NULL, FALSE, FALSE,
          CRM_Contact_BAO_Query::MODE_MEMBER
        );
        $query->_distinctComponentClause = ' civicrm_membership.id';
        $query->_groupByComponentClause = ' GROUP BY civicrm_membership.id ';
        $result = $query->searchQuery(0, 0, $sortOrder);

        while ($result->fetch()) {
          $ids[] = $result->membership_id;
        }
      }
      
      if (!empty($ids)) {
        $form->_componentClause = ' civicrm_membership.id IN ( ' . implode(',', $ids) . ' ) ';
        $form->assign('totalSelectedMembers', count($ids));
      }

      $form->_memberIds = $form->_componentIds = $ids;
      $form->_contactIds = &CRM_Core_DAO::getContactIDsFromComponent($ids,'civicrm_membership');
      $form->assign('totalSelectedContacts', count($form->_contactIds));
    }
    else {
      parent::preProcessCommon($form);
    }
  }
  
  function buildQuickForm() {
    $mysql = 'SELECT id FROM veda_civicrm_wordmailmerge'; 
    $tableCount = CRM_Core_DAO::executeQuery($mysql);
    $noofRows = array();
    while ($tableCount->fetch()) {
      $noofRows = $tableCount->id;
    }
    $rowCount = count($noofRows);
    if( $rowCount == 0){
      $this->add('select', 'message_template', ts('Message Template'), array('' => '- select -'), TRUE);
      CRM_Core_Session::setStatus(ts("No attachement in the template."));
    }else{  
      $sql = " SELECT cmt.id, cmt.msg_title FROM civicrm_msg_template cmt 
               RIGHT JOIN veda_civicrm_wordmailmerge vcw ON ( vcw.msg_template_id = cmt.id)";
      $dao = CRM_Core_DAO::executeQuery($sql);
      while ($dao->fetch()) {
        $msgTemplatesResult[$dao->id] = $dao->msg_title;
      }
      // add form elements
      $this->add('select', 'message_template', ts('Message Template'), array('' => '- select -') + $msgTemplatesResult, TRUE);
      $this->addButtons(array(
        array(
          'type' => 'submit',
          'name' => ts('Merge'),
          'isDefault' => TRUE,
        ),
      ));
    }
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->_contactIds;
    $config = CRM_Core_Config::singleton();
    $msg_id = $this->_submitValues['message_template'];
    if(!empty($msg_id)){
      $mysql =  " SELECT * FROM veda_civicrm_wordmailmerge WHERE msg_template_id = %1"; 
      $params = array(1 => array($msg_id, 'Integer'));
      $dao = CRM_Core_DAO::executeQuery($mysql, $params);
      //$dao = CRM_Core_DAO::executeQuery($mysql);
      while ($dao->fetch()) {
        $fileId = $dao->file_id;
      }
      $sql = "SELECT * FROM civicrm_file WHERE id = %1";
      $params = array(1 => array($fileId, 'Integer'));
      $dao = CRM_Core_DAO::executeQuery($sql, $params);
      //$dao = CRM_Core_DAO::executeQuery($sql);
        while ($dao->fetch()) {
          $default['fileID']        = $dao->id;
          $default['mime_type']     = $dao->mime_type;
          $default['fileName']      = $dao->uri;
          $default['cleanName']     = CRM_Utils_File::cleanFileName($dao->uri);
          $default['fullPath']      = $config->customFileUploadDir . DIRECTORY_SEPARATOR . $dao->uri;
          $default['deleteURLArgs'] = CRM_Core_BAO_File::deleteURLArgs('civicrm_file', $msg_id, $dao->id);
        }
      $defaults[$dao->id] = $default;
      $this->assign('defaults', $defaults);
      $noofContact = count($this->_contactIds);
      require_once $config->extensionsDir.'/uk.co.vedaconsulting.module.wordmailmerge/tinybutstrong/tbs_class.php';
      require_once $config->extensionsDir.'/uk.co.vedaconsulting.module.wordmailmerge/tinybutstrong-opentbs/tbs_plugin_opentbs.php';
      $TBS = new clsTinyButStrong; // new instance of TBS
      $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin
      $template = $default['fullPath'];

      foreach ($values as $key => $value) {
        if($key < $noofContact){
          $selectedCID = $values[$key];
          // get the details for all selected contacts ( to, cc and bcc contacts )
          list($contactFormatted) = CRM_Utils_Token::getTokenDetails(array($selectedCID),
            $this->_returnProperties,
            NULL, NULL, FALSE,
            $this->_allTokens 
          );

          $membershipFormatted = array();
          if ($this->_searchFrom == 'member' && isset($contactFormatted[$selectedCID]['membership_id'])) {
            $membershipFormatted = CRM_Utils_Token::getMembershipTokenDetails($contactFormatted[$selectedCID]['membership_id']);
          }

          foreach ($this->_tokenMerge as $atKey => $atValue) {
            // Replace hook tokens
            $explodedTokenName = explode('.', $atValue['token_name']);
            if (array_key_exists($atValue['token_name'], $contactFormatted[$selectedCID]) ) {
              if (!empty($explodedTokenName[1]) && $explodedTokenName[0] != 'contact') {
                $vars[$key][$explodedTokenName[0]][$explodedTokenName[1]] = $contactFormatted[$selectedCID][$atValue['token_name']];
              }
              else{
                $vars[$key][$atValue['token_name']] = $contactFormatted[$selectedCID][$atValue['token_name']];
              }
            }
            else {
              if ($explodedTokenName[0] == 'membership') {
                $explodedTokenName[1] = ($explodedTokenName[1] == 'membership_id') ? 'id' : $explodedTokenName[1];
                $vars[$key][$explodedTokenName[0]][$explodedTokenName[1]] = CRM_Utils_Token::getMembershipTokenReplacement($explodedTokenName[1], $membershipFormatted[$contactFormatted[$selectedCID]['membership_id']]);
              }
              else {
                $vars[$key][$atValue['token_name']] = CRM_Utils_Token::getContactTokenReplacement($atValue['token_name'], $contactFormatted[$selectedCID]);
              }
            } 
          } 
          $TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
          $TBS->MergeBlock(self::TOKEN_VAR_NAME,$vars);
        }
      }

      $output_file_name = 'CiviCRMWordExport.docx';
      $TBS->Show(OPENTBS_DOWNLOAD, $output_file_name);
      CRM_Utils_System::civiExit();
    }
    parent::postProcess();
  }
  
  function getContact($selectedCID) {
    $result = civicrm_api3('Contact', 'getsingle', array(
                           'sequential' => 1,
                           'contact_id' => $selectedCID,
    ));
    return $result;
  }
  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
