<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Wordmailmerge_Form_Search extends CRM_Core_Form {
  CONST CG_RELATED_MEMBERS = 'Related_Members';
  CONST CF_RM_FIRST_NAME = 'First_Name';
  CONST CF_RM_SECOND_NAME = 'Second_Name';
  CONST CF_RM_RELATIONSHIP = 'Relationship';
  /**
   * Build the form
   *
   * @access public
   *
   * @return void
   */
  function buildQuickForm() {

    $this->add('text', 'sort_name', ts('Surname'));
    $this->add('text', 'postal_code', ts('Postcode'));
    $this->add('text', 'external_identifier', ts('Membership Number'));
    $this->add('hidden', 'contact_id', CRM_Utils_Request::retrieve( 'cid', 'Positive', $this ));

    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->addButtons(array(
      array(
        'type' => 'refresh',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }
  
  function getRenderableElementNames() {
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }


  /**
   * processing needed for buildForm and later
   *
   * @return void
   * @access public
   */
  function preProcess() {
    $this->set('searchFormName', 'Basic');
    $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
    require_once 'CRM/Core/Session.php';
    $session = CRM_Core_Session::singleton();
    $searchContext = $session->get('searchContext');
    
    $this->_reset = CRM_Utils_Request::retrieve('reset', 'Boolean',
      CRM_Core_DAO::$_nullObject
    );

    $this->assign('resetResult', $this->_reset ? 0 : 1);
    if ($session->get('searchContext') && $this->_reset) {
      $session->resetScope('searchContext');
    }
   
    if ($this->_contactId) {
      unset($searchContext);
      $searchContext['contact_id'] = $this->_contactId;
    }
    $this->_columnHeaders = array(
        'name'              => ts('Name'),
        'adult_name'        => ts('Adult Name'),
        'membership_type'   => ts('Membership Type'),
        'address'           => ts('Address'),
        'end_date'          => ts('Expiry date'),      
        'contribution'      => ts('Last Two Contributions'),      
        // 'action'            => ts('Action')
    );
    $this->assign('columnHeaders', $this->_columnHeaders);

    $rows = $this->getContent($searchContext);
    if ($rows) {
     $this->assign('rows', $rows); 
    }
    else{
      $this->assign('rowsEmpty', TRUE);
    }
    
    $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
    parent::preProcess();
  }

  /**
   * @return array
   */
  function &getFormValues() {
    return $this->_formValues;
  }
  
  function getContent($searchContext) {
    if (CRM_Utils_Array::crmIsEmptyArray($searchContext)) {
      return CRM_Core_DAO::$_nullArray;
    }
    $searchBy = FALSE;
    $whereClause = "";
    if (!empty($searchContext['sort_name'])) {
      $sort_name = $searchContext['sort_name'];
      $whereClause .= " AND cc.sort_name like '%{$sort_name}%'";
      $searchBy = TRUE;
    }
    
    if (!empty($searchContext['postal_code'])) {
      $postal_code = $searchContext['postal_code'];
      $whereClause .= " AND ca.postal_code like '{$postal_code}%'";
      $searchBy = TRUE;
    }
    
    if (!empty($searchContext['external_identifier'])) {
      $external_identifier = $searchContext['external_identifier'];
      $whereClause .= " AND cc.external_identifier = {$external_identifier}";
      $searchBy = TRUE;
    }
    
    if (!empty($searchContext['contact_id'])) {
      $searchBy = TRUE;
      $contact_id = $searchContext['contact_id'];
      $whereClause = " AND cc.id = {$contact_id}";
    }
    
    if (!$searchBy) {
      return array();
    }
    
    //custom group details 
    $cGroup = civicrm_api('CustomGroup', 'get', array('version' => 3, 'sequential' => 1, 'name' => self::CG_RELATED_MEMBERS));
    if (!$cGroup['count']) {
      return CRM_Core_DAO::$_nullArray;
    }
    $cgID = $cGroup['values'][0]['id'];
    $cgRelatedMemberTableName = $cGroup['values'][0]['table_name'];
    // print_r($cGroup);
    // die();
    //custom Field detials 
    $cField = civicrm_api('CustomField', 'get', array('version' => 3, 'sequential' => 1, 'custom_group_id' => $cgID));
    if (!$cField['count']) {
      return CRM_Core_DAO::$_nullArray;
    }
    $fnColumnName = $snColumnName = $relationshipColumnName = "";
    foreach ($cField['values'] as $key => $value) {
      if ($value['name'] == self::CF_RM_FIRST_NAME) {
        $fnColumnName = $value['column_name'];
      }
      
      if ($value['name'] == self::CF_RM_SECOND_NAME) {
        $snColumnName = $value['column_name'];
      }
      
      if ($value['name'] == self::CF_RM_RELATIONSHIP) {
        $relationshipColumnName = $value['column_name'];
      }
    }
    
    $adultNameSelectClause = ($fnColumnName && $snColumnName) ? "CONCAT(cvrm.first_name_23, ' ',cvrm.second_name_24)" : "NULL"; 
    $relatedAdult = "";
    if ($relationshipColumnName) {
      $relatedAdult = " AND cvrm.{$relationshipColumnName} = 'ADULT'";
    }
    
    $query = "
    SELECT cc.id as contact_id 
    , cc.display_name as name
    , {$adultNameSelectClause} as other_adult_name
    , cmt.name as membership_type
    , CONCAT(ca.street_address, ' ',ca.postal_code) as street_address
    , cm.end_date as end_date
    FROM civicrm_contact cc 
    LEFT JOIN civicrm_membership cm ON ( cm.contact_id = cc.id ) 
    LEFT JOIN civicrm_membership_type cmt ON ( cmt.id = cm.membership_type_id ) 
    LEFT JOIN civicrm_address ca ON ( ca.contact_id = cc.id )
    LEFT JOIN {$cgRelatedMemberTableName} cvrm ON ( cvrm.entity_id = cc.id {$relatedAdult}) 
    WHERE (1) {$whereClause}
    ORDER BY cc.display_name
    ";

    $dao = CRM_Core_DAO::executeQuery($query);
    $returnResult = array();
    while ($dao->fetch()) {
      $lastContribute = array();
      $contributeDAO = CRM_Core_DAO::executeQuery( "
      SELECT cc.receive_date as receive_date, ft.name as type, cc.total_amount as total_amount
      FROM civicrm_contribution cc
      INNER JOIN civicrm_financial_type ft ON ( ft.id = cc.financial_type_id ) 
      WHERE cc.contact_id IN ( {$dao->contact_id} )
      ORDER BY receive_date DESC 
      LIMIT 2
      ");
      while ($contributeDAO->fetch()) {
        $lastContribute[] =  sprintf("%s - %s - %s",  
          date('F jS Y', strtotime($contributeDAO->receive_date)), 
          $contributeDAO->type, 
          CRM_Utils_Money::format($contributeDAO->total_amount)
          );
      }
      
      $returnResult[$dao->contact_id] = array(
        'name'              => $dao->name,
        'adult_name'        => $dao->other_adult_name,
        'membership_type'   => $dao->membership_type,
        'address'           => $dao->street_address,
        'end_date'          => $dao->end_date,
        'last_contribution' => !empty($lastContribute) ? implode('<br />', $lastContribute) : NULL,
      );
    }

    return $returnResult;
  }
  
  /**
   * this method is called for processing a submitted search form
   *
   * @return void
   * @access public
   */
  function postProcess() {
    $this->_formValues = $this->controller->exportValues($this->_name);
    require_once 'CRM/Core/Session.php';
    $session = CRM_Core_Session::singleton();
    if ($session->get('searchContext')) {
      $session->resetScope('searchContext');
    }
    
    $searchContext = array(
      'sort_name' => $this->_formValues['sort_name'],
      'postal_code' => $this->_formValues['postal_code'],
      'external_identifier' => $this->_formValues['external_identifier'],
      'contact_id' => $this->_formValues['contact_id'],
    );
    
    $session->set('searchContext' , $searchContext);
  }

  /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  /**
   * @return string
   */
  function getTitle() {
    return ts('Find Contacts');
  }
}

