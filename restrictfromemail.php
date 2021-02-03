<?php

require_once 'restrictfromemail.civix.php';
// phpcs:disable
use CRM_Restrictfromemail_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function restrictfromemail_civicrm_config(&$config) {
  _restrictfromemail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function restrictfromemail_civicrm_xmlMenu(&$files) {
  _restrictfromemail_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function restrictfromemail_civicrm_install() {
  _restrictfromemail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function restrictfromemail_civicrm_postInstall() {
  _restrictfromemail_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function restrictfromemail_civicrm_uninstall() {
  _restrictfromemail_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function restrictfromemail_civicrm_enable() {
  _restrictfromemail_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function restrictfromemail_civicrm_disable() {
  _restrictfromemail_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function restrictfromemail_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _restrictfromemail_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function restrictfromemail_civicrm_managed(&$entities) {
  _restrictfromemail_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function restrictfromemail_civicrm_caseTypes(&$caseTypes) {
  _restrictfromemail_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function restrictfromemail_civicrm_angularModules(&$angularModules) {
  _restrictfromemail_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function restrictfromemail_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _restrictfromemail_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function restrictfromemail_civicrm_entityTypes(&$entityTypes) {
  _restrictfromemail_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function restrictfromemail_civicrm_themes(&$themes) {
  _restrictfromemail_civix_civicrm_themes($themes);
}

/**
 *  Implements hook_civicrm_buildForm().
 */
function restrictfromemail_civicrm_buildForm($formName, &$form) {
  if (in_array($formName,
    [
      'CRM_Mailing_Form_Upload',
      'CRM_Contact_Form_Task_Email',
      'CRM_Contribute_Form_Contribution',
      'CRM_Event_Form_Participant',
      'CRM_Member_Form_Membership',
      'CRM_Pledge_Form_Pledge',
      'CRM_Contribute_Form_Task_Email',
      'CRM_Event_Form_Task_Email',
      'CRM_Member_Form_Task_Email',
    ])) {

    $fromField = 'from_email_address';
    if (in_array($formName,
      [
        'CRM_Contribute_Form_Task_Email',
        'CRM_Event_Form_Task_Email',
        'CRM_Member_Form_Task_Email',
      ])) {
      $fromField = 'fromEmailAddress';
    }

    if (!$form->elementExists($fromField)) {
      return NULL;
    }

    $elements = & $form->getElement($fromField);
    $options = & $elements->_options;
    suppressEmails($options);

    if (empty($options)) {
      $options = [
        [
          'text' => ts('- Select -'),
          'attr' => ['value' => ''],
        ],
      ];
    }
    $options = array_values($options);
  }
}

/**
 * Function to supress email address.
 *
 * @param array $fromEmailAddress
 * @param bool $showNotice
 *
 * @return array|NULL
 */
function suppressEmails(&$fromEmailAddress) {

  $cid = CRM_Core_Session::singleton()->getLoggedInContactId();
  // Get email addresses associated with contact.
  $emails = civicrm_api3('Email', 'get', [
    'contact_id' => $cid,
    'on_hold' => 0,
  ]);

  if (!empty($emails['values'])) {
    // Loop through all the From emails passed in to check if they should be suppressed or not
    foreach ($fromEmailAddress as $keys => $headers) {
      if (!array_key_exists($headers['attr']['value'], $emails['values'])) {
        unset($fromEmailAddress[$keys]);
      }
    }
  }

  return NULL;
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function restrictfromemail_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function restrictfromemail_civicrm_navigationMenu(&$menu) {
//  _restrictfromemail_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _restrictfromemail_civix_navigationMenu($menu);
//}
