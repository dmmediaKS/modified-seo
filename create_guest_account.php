<?php
/* -----------------------------------------------------------------------------------------
   $Id: create_guest_account.php 16448 2025-05-13 12:15:42Z AGI $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account.php,v 1.63 2003/05/28); www.oscommerce.com
   (c) 2003 nextcommerce (create_account.php,v 1.27 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce (create_guest_account.php 176 2007-02-15)

   Released under the GNU General Public License
   Guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

defined('DISPLAY_PRIVACY_CHECK') or define('DISPLAY_PRIVACY_CHECK', 'true');

// captcha
$use_captcha = array();
if (defined('MODULE_CAPTCHA_ACTIVE')) {
  $use_captcha = explode(',', MODULE_CAPTCHA_ACTIVE);
}
defined('MODULE_CAPTCHA_CODE_LENGTH') or define('MODULE_CAPTCHA_CODE_LENGTH', 6);
defined('MODULE_CAPTCHA_LOGGED_IN') or define('MODULE_CAPTCHA_LOGGED_IN', 'True');

// redirect to create_account if creation of guest accounts is not enabled
if (ACCOUNT_OPTIONS == 'account') {
  xtc_redirect(FILENAME_DEFAULT);
}
if (isset($_SESSION['customer_id'])) {
  xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

$products = $_SESSION['cart']->get_products();
for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {
  if (preg_match('/^GIFT/', addslashes($products[$i]['model']))) {
    xtc_redirect(xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
  }
}

// include needed functions
require_once (DIR_FS_INC.'xtc_get_country_list.inc.php');
require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
require_once (DIR_FS_INC.'xtc_create_password.inc.php');
require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');
require_once (DIR_FS_INC.'get_customers_gender.inc.php');
require_once (DIR_FS_INC.'check_country_required_zones.inc.php');
require_once (DIR_FS_INC.'secure_form.inc.php');
require_once (DIR_FS_INC.'write_customers_session.inc.php');

// include needed classes
require_once (DIR_WS_CLASSES.'modified_captcha.php');

// create smarty elements
$smarty = new Smarty();

$mod_captcha = $_mod_captcha_class::getInstance();

if (!isset($_POST['country'])) {
  $country = isset($_SESSION['country']) ? (int)$_SESSION['country'] : STORE_COUNTRY; //is country_id (int)
} else {
  $country = (int)$_POST['country'];
}
$privacy = isset($_POST['privacy']) && $_POST['privacy'] == 'privacy' ? true : false;

$required_zones = check_country_required_zones($country);

$process = false;
if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
  $process = true;

  $valid_params = array(
    'gender',
    'firstname',
    'lastname',
    'street_address',
    'postcode',
    'city',
    'country',
    'state',
    'company',
    'suburb',
    'email_address',
    'confirm_email_address',
    'vat',
    'newsletter',
    'telephone',
    'fax',
    'dob',
  );

  // prepare variables
  foreach ($_POST as $key => $value) {
    if ((!isset(${$key}) || !is_object(${$key})) && in_array($key , $valid_params)) {
      ${$key} = xtc_db_prepare_input($value);
    }
  }

  $error = false;
  
  if (mb_strlen($firstname, $_SESSION['language_charset']) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
  }

  if (mb_strlen($lastname, $_SESSION['language_charset']) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
  }

  if (ACCOUNT_DOB == 'true') {
    $date = xtc_date_raw($dob);
    if (is_numeric($date) == false
        || strlen($date) != 8
        || checkdate(substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4)) == false
        )
    {
      $error = true;
      $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
    }
  }

  // New VAT Check
  if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
    if (!isset($vat)) $vat = '';
    require_once (DIR_WS_CLASSES . 'vat_validation.php');
    $vatID = new vat_validation($vat, '', '', (int)$country, true);
    $customers_status = $vatID->vat_info['status'];
    $customers_vat_id_status = isset($vatID->vat_info['vat_id_status']) ? $vatID->vat_info['vat_id_status'] : '';
    if (isset($vatID->vat_info['error']) && $vatID->vat_info['error']==1){
      $messageStack->add('create_account', ENTRY_VAT_ERROR);
      $error = true;
    }
  }

  // email check
  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
  } elseif (xtc_validate_email($email_address) == false) {
    $error = true;
    $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
  } elseif ($email_address != $confirm_email_address) {
    $error = true;
    $messageStack->add('create_account', ENTRY_EMAIL_ERROR_NOT_MATCHING);
  }

  if (mb_strlen($street_address, $_SESSION['language_charset']) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
  }

  if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
  }

  if (mb_strlen($city, $_SESSION['language_charset']) < ENTRY_CITY_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_CITY_ERROR);
  }

  if (is_numeric($country) == false) {
    $error = true;
    $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
  } else {
    $check_country_query = xtc_db_query("SELECT countries_id
                                           FROM ".TABLE_COUNTRIES."
                                          WHERE countries_id = '".(int)$country."'
                                            AND status = '1'");
    if (xtc_db_num_rows($check_country_query) < 1) {
      $error = true;
      $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
    }
  }

  $entry_state_has_zones = false;
  if (ACCOUNT_STATE == 'true') {
    $zone_id = 0;
    $check_query = xtc_db_query("SELECT count(*) AS total 
                                   FROM ".TABLE_ZONES." z
                                   JOIN ".TABLE_COUNTRIES." c 
                                        ON c.countries_id = z.zone_country_id 
                                          AND c.required_zones = '1' 
                                  WHERE z.zone_country_id = '".(int)$country."'");
    $check = xtc_db_fetch_array($check_query);
    $entry_state_has_zones = ($check['total'] > 0);
    if ($entry_state_has_zones == true) {
        $zone_query = xtc_db_query("SELECT DISTINCT zone_id
                                               FROM ".TABLE_ZONES."
                                              WHERE zone_country_id = '".(int)$country ."'
                                                AND (zone_id = '" . (int)$state . "'
                                                     OR zone_code = '" . xtc_db_input($state) . "'
                                                     OR zone_name LIKE '" . xtc_db_input($state) . "%'
                                                     )");
      if (xtc_db_num_rows($zone_query) == 1) {
        $zone = xtc_db_fetch_array($zone_query);
        $zone_id = $zone['zone_id'];
        $state = '';
      } else {
        $error = true;
        $messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
      }
    } else {
      if (!$required_zones) {
        $state = '';
      } elseif (mb_strlen($state, $_SESSION['language_charset']) < ENTRY_STATE_MIN_LENGTH) {
        $error = true;
        $messageStack->add('create_account', ENTRY_STATE_ERROR);
      }
    }
  }

  if (ACCOUNT_TELEPHONE_OPTIONAL == 'false' && strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
  }

  if (DISPLAY_PRIVACY_CHECK == 'true' && empty($privacy)) {
    $error = true;
    $messageStack->add('create_account', ENTRY_PRIVACY_ERROR);
  }

  if (in_array('create_account', $use_captcha)) {
    if ($mod_captcha->validate((isset($_POST['vvcode'])) ? $_POST['vvcode'] : '') !== true) {
      $messageStack->add('create_account', strip_tags(ERROR_VVCODE, '<b><strong>'));
      $error = true;
    }
  }
  
  if (check_secure_form($_POST) === false) {
    $messageStack->add('create_account', ENTRY_TOKEN_ERROR);
    $error = true;
  }

  if ($error == false) {
    $customers_password_time = time();
    
    if (!isset($customers_status) || (int)$customers_status == 0) {
      if (DEFAULT_CUSTOMERS_STATUS_ID_GUEST != 0) {
        $customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
      } else {
        $customers_status = 1;
      }
    }
    
    $sql_data_array = array(
      'customers_status' => (int)$customers_status,
      'customers_firstname' => $firstname,
      'customers_lastname' => $lastname,
      'customers_email_address' => $email_address,
      'customers_telephone' => ((isset($telephone)) ? $telephone : ''),
      'customers_fax' => ((isset($fax)) ? $fax : ''),
      'customers_newsletter' => ((isset($newsletter)) ? (int)$newsletter : 0),
      'account_type' => '1',
      'customers_password' => xtc_create_password(8),
      'customers_password_time' => $customers_password_time,
      'customers_date_added' => 'now()',
      'customers_last_modified' => 'now()',
    );

    if (ACCOUNT_GENDER == 'true') {
      $sql_data_array['customers_gender'] = $gender;
    }
    if (ACCOUNT_DOB == 'true') {
      $sql_data_array['customers_dob'] = xtc_date_raw($dob);
    }
    if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
      $sql_data_array['customers_vat_id'] = $vat;
      $sql_data_array['customers_vat_id_status'] = $customers_vat_id_status;
    }

    xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

    $_SESSION['customer_id'] = xtc_db_insert_id();
    $_SESSION['customer_time'] = $customers_password_time;
    
    $sql_data_array = array(
      'customers_id' => $_SESSION['customer_id'],
      'entry_firstname' => $firstname,
      'entry_lastname' => $lastname,
      'entry_street_address' => $street_address,
      'entry_postcode' => $postcode,
      'entry_city' => $city,
      'entry_country_id' => (int)$country,
      'address_date_added' => 'now()',
      'address_last_modified' => 'now()'
    );

    if (ACCOUNT_GENDER == 'true') {
      $sql_data_array['entry_gender'] = $gender;
    }
    if (ACCOUNT_COMPANY == 'true') {
      $sql_data_array['entry_company'] = $company;
    }
    if (ACCOUNT_SUBURB == 'true') {
      $sql_data_array['entry_suburb'] = $suburb;
    }
    if (ACCOUNT_STATE == 'true') {
      $sql_data_array['entry_zone_id'] = (isset($zone_id) ? (int)$zone_id : 0);
      $sql_data_array['entry_state'] = ((isset($state) && !empty($state)) ? $state : '');
    }

    xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

    $address_id = xtc_db_insert_id();

    xtc_db_query("UPDATE " . TABLE_CUSTOMERS . " 
                     SET customers_default_address_id = '" . (int)$address_id . "' 
                   WHERE customers_id = '" . (int)$_SESSION['customer_id'] . "'");
    
    $sql_data_array = array(
      'customers_info_id' => (int)$_SESSION['customer_id'],
      'customers_info_number_of_logons' => '1',
      'customers_info_date_account_created' => 'now()',
      'customers_info_date_of_last_logon' => 'now()'
    );
    xtc_db_perform(TABLE_CUSTOMERS_INFO, $sql_data_array);

    if (SESSION_RECREATE == 'True') {
      xtc_session_recreate();
    }
    
    // write customers session
    write_customers_session((int)$_SESSION['customer_id']);
  
    // user info
    xtc_write_user_info((int)$_SESSION['customer_id']);

    // restore cart contents
    $_SESSION['cart']->restore_contents();

	  // campaign tracking
    if (isset($_SESSION['tracking']['refID'])) {
      $campaign_check = xtc_db_query("SELECT campaigns_id, 
                                             campaigns_leads
                                        FROM ".TABLE_CAMPAIGNS."
                                       WHERE campaigns_refID = '".xtc_db_input($_SESSION['tracking']['refID'])."'");
      if (xtc_db_num_rows($campaign_check) > 0) {
        $campaign = xtc_db_fetch_array($campaign_check);
      
        xtc_db_query("UPDATE ".TABLE_CUSTOMERS."
                         SET refferers_id = '".(int)$campaign['campaigns_id']."'
                       WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
      
        xtc_db_query("UPDATE ".TABLE_CAMPAIGNS."
                         SET campaigns_leads = campaigns_leads + 1
                       WHERE campaigns_id = '".(int)$campaign['campaigns_id']."'");
      }
    }

    if (isset($newsletter) && $newsletter == '1') {
      require_once (DIR_WS_CLASSES.'class.newsletter.php');
      $newsletter = new newsletter;
      $newsletter->AddUserAuto($email_address);
    }

    if ($_SESSION['cart']->count_contents() > 0) {
      xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
    xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  }
}

// build breadcrumb
$breadcrumb->add(NAVBAR_TITLE_CREATE_GUEST_ACCOUNT, xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));

// include header
require (DIR_WS_INCLUDES . 'header.php');

// include boxes
$display_mode = 'createaccount';
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

if ($messageStack->size('create_account') > 0) {
  $smarty->assign('error_message', $messageStack->output('create_account'));
}

$smarty->assign('FORM_ACTION', xtc_draw_form('create_account', xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'), 'post').xtc_draw_hidden_field('action', 'process').secure_form('create_account'));

if (ACCOUNT_GENDER == 'true') {
  $smarty->assign('gender', '1');
  $smarty->assign('INPUT_GENDER', xtc_draw_pull_down_menuNote(array('name' => 'gender', 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">'.ENTRY_GENDER_TEXT.'</span>' : '')), get_customers_gender(), ((isset($gender)) ? $gender : '')));
} else {
  $smarty->assign('gender', '0');
}

$smarty->assign('INPUT_FIRSTNAME', xtc_draw_input_fieldNote(array('name' => 'firstname', 'text' => (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : ''))));
$smarty->assign('INPUT_LASTNAME', xtc_draw_input_fieldNote(array('name' => 'lastname', 'text' => (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : ''))));

if (ACCOUNT_DOB == 'true') {
  $smarty->assign('birthdate', '1');
  $smarty->assign('INPUT_DOB', xtc_draw_input_fieldNote(array('name' => 'dob', 'text' => (xtc_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>' : ''))));
  $smarty->assign('TEXT_DOB_NOTE', ENTRY_DATE_OF_BIRTH_NOTE);
} else {
  $smarty->assign('birthdate', '0');
}

$smarty->assign('INPUT_EMAIL', xtc_draw_input_fieldNote(array('name' => 'email_address', 'text' => (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>' : '')), '',''));
$smarty->assign('INPUT_CONFIRM_EMAIL', xtc_draw_input_fieldNote(array('name' => 'confirm_email_address', 'text' => (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : '')), '',''));

if (ACCOUNT_COMPANY == 'true') {
  $smarty->assign('company', '1');
  $smarty->assign('INPUT_COMPANY', xtc_draw_input_fieldNote(array('name' => 'company', 'text' => (xtc_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>' : ''))));
} else {
  $smarty->assign('company', '0');
}

if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
  $smarty->assign('vat', '1');
  $smarty->assign('INPUT_VAT', xtc_draw_input_fieldNote(array('name' => 'vat', 'text' => (xtc_not_null(ENTRY_VAT_TEXT) ? '<span class="inputRequirement">' . ENTRY_VAT_TEXT . '</span>' : ''))));
  $smarty->assign('TEXT_VAT_NOTE', ENTRY_VAT_NOTE);
} else {
  $smarty->assign('vat', '0');
}

$smarty->assign('INPUT_STREET', xtc_draw_input_fieldNote(array('name' => 'street_address', 'text' => (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : ''))));

if (ACCOUNT_SUBURB == 'true') {
  $smarty->assign('suburb', '1');
  $smarty->assign('INPUT_SUBURB', xtc_draw_input_fieldNote(array('name' => 'suburb', 'text' => (xtc_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>' : ''))));
} else {
  $smarty->assign('suburb', '0');
}

$smarty->assign('INPUT_CODE', xtc_draw_input_fieldNote(array('name' => 'postcode', 'text' => (xtc_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>' : ''))));
$smarty->assign('INPUT_CITY', xtc_draw_input_fieldNote(array('name' => 'city', 'text' => (xtc_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>' : ''))));

if (ACCOUNT_STATE == 'true') { //important no $required_zones because of ajax loader
  $smarty->assign('state', '1');
  $smarty->assign('display_state', '');
  if ($process == true) {
    if ($entry_state_has_zones == true) {
      $zones_array = array();
      $zones_query = xtDBquery("SELECT zone_id, 
                                       zone_name 
                                  FROM " . TABLE_ZONES . " 
                                 WHERE zone_country_id = '" . (int)$country . "' 
                              ORDER BY zone_name");
      while ($zones_values = xtc_db_fetch_array($zones_query, true)) {
        $zones_array[] = array(
          'id' => $zones_values['zone_id'],
          'text' => $zones_values['zone_name']
        );
      }
      $state_input = xtc_draw_pull_down_menuNote(array('name' => 'state', 'text' => (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')), $zones_array, $zone_id);
    } else {
      $state_input = xtc_draw_input_fieldNote(array('name' => 'state', 'text' => (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')));
      if (!$required_zones) {
        $state_input = '<input type="hidden" value="0" name="state">';
        $smarty->assign('display_state', ' style="display:none"');        
      }
    }
  } else {
    $state_input = xtc_draw_input_fieldNote(array('name' => 'state', 'text' => (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')));
    if (!$required_zones) {
      $state_input = '<input type="hidden" value="0" name="state">';
      $smarty->assign('display_state', ' style="display:none"');     
    }
  }
  $smarty->assign('INPUT_STATE', $state_input);
} else {
  $smarty->assign('state', '0');
}

if (ACCOUNT_FAX == 'true') {
  $smarty->assign('fax', '1');
  $smarty->assign('INPUT_FAX', xtc_draw_input_fieldNote(array('name' => 'fax', 'text' => (xtc_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_FAX_NUMBER_TEXT . '</span>' : ''))));
} else {
  $smarty->assign('fax', '0');
}

if (in_array('create_account', $use_captcha)) {
  $smarty->assign('VVIMG', $mod_captcha->get_image_code());
  $smarty->assign('INPUT_VVCODE', $mod_captcha->get_input_code());
}
$smarty->assign('SELECT_COUNTRY', xtc_get_country_list(array('name' => 'country', 'text' => (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : '')), (int)$country));
$smarty->assign('INPUT_TEL', xtc_draw_input_fieldNote(array('name' => 'telephone', 'text' => ((ACCOUNT_TELEPHONE_OPTIONAL == 'false' && xtc_not_null(ENTRY_TELEPHONE_NUMBER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>' : ''))));
if (defined('MODULE_NEWSLETTER_STATUS') && MODULE_NEWSLETTER_STATUS == 'true') {
  $smarty->assign('CHECKBOX_NEWSLETTER', xtc_draw_checkbox_field('newsletter', '1', false, 'id="newsletter"').(xtc_not_null(ENTRY_NEWSLETTER_TEXT) ? '<span class="inputRequirement">'.ENTRY_NEWSLETTER_TEXT.'</span>' : ''));
}
if (DISPLAY_PRIVACY_CHECK == 'true') {
  $smarty->assign('PRIVACY_CHECKBOX', xtc_draw_checkbox_field('privacy', 'privacy', $privacy, 'id="privacy"'));
}
$smarty->assign('PRIVACY_LINK', $main->getContentLink(2, MORE_INFO, $request_type));
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('BUTTON_SUBMIT_SAVE', xtc_image_submit('button_save.gif', IMAGE_BUTTON_SAVE));

$main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/create_account_guest.html');
$smarty->assign('main_content', $main_content);
if (!defined('RM'))
  $smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include ('includes/application_bottom.php');