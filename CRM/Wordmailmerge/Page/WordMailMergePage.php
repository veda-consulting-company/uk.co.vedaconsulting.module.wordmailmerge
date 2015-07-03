<?php

require_once 'CRM/Core/Page.php';

class CRM_Wordmailmerge_Page_WordMailMergePage extends CRM_Core_Page {
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Process QR Code'));
    
    $contactID    = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $membershipID = CRM_Utils_Request::retrieve('mid', 'Positive', $this, FALSE);
    $userChecksum = CRM_Utils_Request::retrieve('cs', 'String', $this, TRUE);
    if (!CRM_Contact_BAO_Contact_Utils::validChecksum($contactID, $userChecksum)) {
      CRM_Utils_System::setStatus('Invalid Contact');
      CRM_Utils_System::redirect('civicrm/dashboard');
    }

    // Example: Assign a variable for use in a template
    $this->assign('cid', $contactID);
    $this->assign('mid', $membershipID);
    $this->assign('name', CRM_Contact_BAO_Contact::displayName($contactID));

    parent::run();
  }
}
