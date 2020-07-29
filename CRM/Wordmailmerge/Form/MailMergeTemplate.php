<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Wordmailmerge_Form_MailMergeTemplate extends CRM_Contribute_Form {  
  
  public function buildQuickForm(){

    $this->add('text', 'title', ts('Template Title'),'' ,TRUE);
    $this->add('File', 'uploadFile', ts('Upload the file'), 'size=30 maxlength=255', TRUE);
    $this->add('select', 'mail_merge', ts('Mail merge'),
      array(
        '' => ts('- select -'),
        '1' => ts('Libreoffice Calc template '),
        '2' => ts('Ms Excel template')
      ) 
    );

    $this->add('checkbox', 'is_active', ts('Enabled?'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));
  }

  function postprocess(){
   
    $config = CRM_Core_Config::singleton();
    $uploaddir = $config->customFileUploadDir;
    $value = $_FILES['uploadFile'];

    $fileFormat = (explode(".",$value['name']));
    if(isset($fileFormat[1]) && ($fileFormat[1] == 'ods' || $fileFormat[1] == 'xlsx' && !empty($fileFormat[0]))){
      foreach ($this->getSubmitValue('uploadFile') as $key => $values){
        if($key == 'name'){ 
          $fileName = $values;
        }else if($key == 'type'){
          $fileType = $values;
        }
      }
    
      $mime_type = $_FILES['uploadFile']['type'];
      $uploadfile = $uploaddir.$value['name'];
      
      if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)) {
        $sql = "INSERT INTO `civicrm_file` ( mime_type, uri )
                VALUES ( %1, %2 )";
        $params = array(1 => array($mime_type, 'String'), 2 => array($value['name'], 'String'));
        CRM_Core_DAO::executeQuery($sql, $params);
        $query = " SELECT * FROM `civicrm_file` WHERE `uri` = %1";
        $params = array(1 => array($value['name'], 'String'));
        $dao = CRM_Core_DAO::executeQuery($query, $params);
        while ($dao->fetch()) {
          $fileId = $dao->id ;
        }

        $is_active = $this->getSubmitValue('is_active');
        $templateName = $this->getSubmitValue('title');
        $mailMergeOptionId = $this->getSubmitValue('mail_merge'); 
        $sql = "INSERT INTO `veda_civicrm_excelmailmerge` ( template_title, is_active, file_id, mailmerge_option_id) 
        VALUES ( %1 , %2, %3 , %4)";
   
        $params = array(
          1 => array($templateName, 'String'), 
          2 => array($is_active, 'Integer'), 
          3 => array($fileId, 'Integer'),
          4 => array($mailMergeOptionId, 'Integer')
        );
        CRM_Core_DAO::executeQuery($sql, $params);
        $query = " SELECT * FROM `veda_civicrm_excelmailmerge` WHERE `template_title` = %1";
        $params = array(1 => array($templateName, 'String'));
        $dao = CRM_Core_DAO::executeQuery($query, $params);
        while ($dao->fetch()) {
          $templateId = $dao->id ;
        }

        $sql = "INSERT INTO `civicrm_entity_file` ( entity_table, entity_id, file_id )
                  VALUES ( %1, %2, %3 )";
        $params = array(
          1 => array('excel_mailmerge_template', 'String'),
          2 => array($templateId, 'Integer'), 
          3 => array($fileId, 'Integer')
        );
        CRM_Core_DAO::executeQuery($sql, $params);
      }
    }
  }
}
