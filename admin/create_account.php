<?php
  /* -----------------------------------------------------------------------------------------
   $Id: create_account.php 16430 2025-05-02 12:11:12Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(customers.php,v 1.76 2003/05/04); www.oscommerce.com
   (c) 2003 nextcommerce (create_account.php,v 1.17 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require ('includes/application_top.php');
  
  require(DIR_WS_INCLUDES . 'get_states.php');

  require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
  require_once (DIR_FS_INC.'xtc_create_password.inc.php');
  require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');
  require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
  require_once (DIR_FS_INC.'generate_customers_cid.inc.php');
  require_once (DIR_FS_INC.'get_customers_gender.inc.php');

  // initiate template engine for mail
  $smarty = new Smarty();

  $customers_statuses_array = xtc_get_customers_statuses();
  if (!isset($customers_password)) {
    $customers_password_encrypted = xtc_RandomString(8);
    $customers_password = xtc_encrypt_password($customers_password_encrypted);
  }

  $error = false;
  $entry_password_error = false;
  $entry_mail_error = false;
  $entry_firstname_error = false;
  $entry_lastname_error = false;
  $entry_date_of_birth_error = false;
  $entry_vat_error = false;
  $entry_email_address_error = false;
  $entry_email_address_check_error = false;
  $entry_street_address_error = false;
  $entry_post_code_error = false;
  $entry_city_error = false;
  $entry_country_error = false;
  $entry_state_error = false;
  $entry_email_address_exists = false;
  $entry_telephone_error = false;

  if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $customers_firstname = xtc_db_prepare_input($_POST['customers_firstname']);
    $customers_cid = xtc_db_prepare_input($_POST['csID']);
    $customers_vat_id = xtc_db_prepare_input($_POST['customers_vat_id']);
    $customers_lastname = xtc_db_prepare_input($_POST['customers_lastname']);
    $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
    $customers_telephone = xtc_db_prepare_input($_POST['customers_telephone']);
    $customers_fax = xtc_db_prepare_input($_POST['customers_fax']);
    $customers_status_c = xtc_db_prepare_input($_POST['status']);
    
    $default_address_id = xtc_db_prepare_input($_POST['default_address_id']);
    $entry_street_address = xtc_db_prepare_input($_POST['entry_street_address']);
    $entry_suburb = xtc_db_prepare_input($_POST['entry_suburb']);
    $entry_postcode = xtc_db_prepare_input($_POST['entry_postcode']);
    $entry_city = xtc_db_prepare_input($_POST['entry_city']);
    $entry_country_id = xtc_db_prepare_input($_POST['entry_country_id']);
    $entry_company = xtc_db_prepare_input($_POST['entry_company']);

    $customers_send_mail = xtc_db_prepare_input($_POST['customers_mail']);
    $customers_password_encrypted = xtc_db_prepare_input($_POST['entry_password']);
    $customers_password = xtc_encrypt_password($customers_password_encrypted);

    $customers_mail_comments = xtc_db_prepare_input($_POST['mail_comments']);

    $payment_unallowed = implode(',', (isset($_POST['payment_unallowed']) && is_array($_POST['payment_unallowed']) ? $_POST['payment_unallowed'] : array()));
    $shipping_unallowed = implode(',', (isset($_POST['shipping_unallowed']) && is_array($_POST['shipping_unallowed']) ? $_POST['shipping_unallowed'] : array()));

    if ($customers_password == '') {
      $customers_password_encrypted =  xtc_RandomString(8);
      $customers_password = xtc_encrypt_password($customers_password_encrypted);
    }

    if (ACCOUNT_GENDER == 'true') {
      $customers_gender = xtc_db_prepare_input($_POST['customers_gender']);
    }

    if (strlen($customers_password) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = $entry_password_error = true;
    }

    if ($customers_send_mail != 'yes' && $customers_send_mail != 'no') {
      $error = $entry_mail_error = true;
    }

    if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = $entry_firstname_error = true;
    }

    if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = $entry_lastname_error = true;
    }

    if (ACCOUNT_DOB == 'true') {
      $customers_dob = xtc_db_prepare_input($_POST['customers_dob']);
      $date = xtc_date_raw($customers_dob);
      if (is_numeric($date) == false
          || strlen($date) != 8
          || checkdate(substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4)) == false
          )
      {
        $error = $entry_date_of_birth_error = true;
      }
    }

    // VAT Check
    if (xtc_get_geo_zone_code($entry_country_id) != '6') {
      require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'vat_validation.php');
      $vatID = new vat_validation($customers_vat_id, '', '', $entry_country_id);
      $customers_vat_id_status = isset($vatID->vat_info['vat_id_status']) ? $vatID->vat_info['vat_id_status'] : '';
      // display correct error code of VAT ID check
      switch ($customers_vat_id_status) {
        case '0': // VAT invalid
          $entry_vat_error_text = TEXT_VAT_FALSE;
          break;
        case '1': // VAT valid
          $entry_vat_error_text = TEXT_VAT_TRUE;
          break;
        case '2': // SOAP ERROR: Connection to host not possible, europe.eu down?
          $entry_vat_error_text = TEXT_VAT_CONNECTION_NOT_POSSIBLE;
          break;
        case '8': // unknown country
          $entry_vat_error_text = TEXT_VAT_UNKNOWN_COUNTRY;
          break;
        case '94': // INVALID_INPUT => The provided CountryCode is invalid or the VAT number is empty
          $entry_vat_error_text = TEXT_VAT_INVALID_INPUT;
          break;
        case '95': // SERVICE_UNAVAILABLE => The SOAP service is unavailable, try again later
          $entry_vat_error_text = TEXT_VAT_SERVICE_UNAVAILABLE;
          break;
        case '96': // MS_UNAVAILABLE => The Member State service is unavailable, try again later or with another Member State
          $entry_vat_error_text = TEXT_VAT_MS_UNAVAILABLE;
          break;
        case '97': // TIMEOUT => The Member State service could not be reached in time, try again later or with another Member State
          $entry_vat_error_text = TEXT_VAT_TIMEOUT;
          break;
        case '98': // SERVER_BUSY => The service cannot process your request. Try again later
          $entry_vat_error_text = TEXT_VAT_SERVER_BUSY;
          break;
        case '99': // no PHP5 SOAP support
          $entry_vat_error_text = TEXT_VAT_NO_PHP5_SOAP_SUPPORT;
          break;
        default:
          $entry_vat_error_text = '';
          break;
      }
      if (isset($vatID->vat_info['error']) && $vatID->vat_info['error'] == 1) {
        $entry_vat_error = true;
        $error = true;
      }
    }

    if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = $entry_email_address_error = true;
    }

    if (!xtc_validate_email($customers_email_address)) {
      $error = $entry_email_address_check_error = true;
    }

    if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
      $error = $entry_street_address_error = true;
    }

    if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
      $error = $entry_post_code_error = true;
    }

    if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
      $error = $entry_city_error = true;
    }

    if ($entry_country_id == false) {
      $error = $entry_country_error = true;
    }

    if (ACCOUNT_STATE == 'true') {
      $entry_state = xtc_db_prepare_input($_POST['entry_state']);
      
      if ($entry_country_error == true) {
        $entry_state_error = true;
      } else {
        $zone_id = 0;
        $check_query = xtc_db_query("SELECT count(*) as total
                                       FROM ".TABLE_ZONES."
                                      WHERE zone_country_id = '".xtc_db_input($entry_country_id)."'");
        $check_value = xtc_db_fetch_array($check_query);
        $entry_state_has_zones = ($check_value['total'] > 0);
        if ($entry_state_has_zones == true) {
          $zone_query = xtc_db_query("SELECT zone_id
                                        FROM ".TABLE_ZONES."
                                       WHERE zone_country_id = '".xtc_db_input($entry_country_id)."'
                                         AND zone_name = '".xtc_db_input($entry_state)."'");
          if (xtc_db_num_rows($zone_query) == 1) {
            $zone_values = xtc_db_fetch_array($zone_query);
            $zone_id = $zone_values['zone_id'];
          } else {
            $zone_query = xtc_db_query("SELECT zone_id
                                          FROM ".TABLE_ZONES."
                                         WHERE zone_country_id = '".xtc_db_input($entry_country_id)."'
                                           AND zone_code = '".xtc_db_input($entry_state)."'");
            if (xtc_db_num_rows($zone_query) >= 1) {
              $zone_values = xtc_db_fetch_array($zone_query);
              $zone_id = $zone_values['zone_id'];
            } else {
              $error = true;
              $entry_state_error = true;
            }
          }
        }
      }
    }

    if (ACCOUNT_TELEPHONE_OPTIONAL == 'false' && strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = $entry_telephone_error = true;
    }

    $check_email = xtc_db_query("SELECT customers_email_address
                                   FROM ".TABLE_CUSTOMERS."
                                  WHERE customers_email_address = '".xtc_db_input($customers_email_address)."'
                                    AND account_type = '0'");
    if (xtc_db_num_rows($check_email)) {
      $error = $entry_email_address_exists = true;
    }

    if ($error == false) {
      $sql_data_array = array (
          'customers_status' => $customers_status_c,
          'customers_cid' => ((defined('MODULE_CUSTOMERS_CID_STATUS') && MODULE_CUSTOMERS_CID_STATUS == 'true') ? generate_customers_cid(true) : $customers_cid),
          'customers_vat_id' => $customers_vat_id,
          'customers_vat_id_status' => $customers_vat_id_status,
          'customers_firstname' => $customers_firstname,
          'customers_lastname' => $customers_lastname,
          'customers_email_address' => $customers_email_address,
          'customers_telephone' => $customers_telephone,
          'customers_fax' => $customers_fax,
          'payment_unallowed' => $payment_unallowed,
          'shipping_unallowed' => $shipping_unallowed,
          'customers_password' => $customers_password,
          'customers_date_added' => 'now()',
          'customers_last_modified' => 'now()',
          'password_request_time' => 'now()'
        );

      if (ACCOUNT_GENDER == 'true')
        $sql_data_array['customers_gender'] = $customers_gender;
      if (ACCOUNT_DOB == 'true')
        $sql_data_array['customers_dob'] = xtc_date_raw($customers_dob);

      xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);
      $cc_id = xtc_db_insert_id();

      $sql_data_array = array (
          'customers_id' => $cc_id,
          'entry_firstname' => $customers_firstname,
          'entry_lastname' => $customers_lastname,
          'entry_street_address' => $entry_street_address,
          'entry_postcode' => $entry_postcode,
          'entry_city' => $entry_city,
          'entry_country_id' => $entry_country_id,
          'address_date_added' => 'now()',
          'address_last_modified' => 'now()'
        );

      if (ACCOUNT_GENDER == 'true')
        $sql_data_array['entry_gender'] = $customers_gender;
      if (ACCOUNT_COMPANY == 'true')
        $sql_data_array['entry_company'] = $entry_company;
      if (ACCOUNT_SUBURB == 'true')
        $sql_data_array['entry_suburb'] = $entry_suburb;
      if (ACCOUNT_STATE == 'true') {
        $sql_data_array['entry_zone_id'] = (int)$zone_id;
        $sql_data_array['entry_state'] = $entry_state;
      }
      xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
      $address_id = xtc_db_insert_id();

      xtc_db_query("UPDATE ".TABLE_CUSTOMERS." SET customers_default_address_id = '".$address_id."' WHERE customers_id = '".$cc_id."'");

      xtc_db_query("INSERT INTO ".TABLE_CUSTOMERS_INFO." (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) VALUES ('".$cc_id."', '0', now())");

      // Create insert into admin access table if admin is created.
      if ($customers_status_c == '0') {
        xtc_db_query("INSERT INTO ".TABLE_ADMIN_ACCESS." (customers_id,start) VALUES ('".$cc_id."','1')");
      }

      // Create eMail
      if (($customers_send_mail == 'yes')) {
        // set dirs manual
        $smarty->template_dir = DIR_FS_CATALOG.'templates';
        $smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
        $smarty->config_dir = DIR_FS_CATALOG.'lang';
        
        $smarty->assign('tpl_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/');
        $smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

        $smarty->assign('GENDER', $customers_gender);
        $smarty->assign('FIRSTNAME',$customers_firstname);
        $smarty->assign('LASTNAME',$customers_lastname);
        $smarty->assign('NAME', $customers_firstname.' '.$customers_lastname);
        $smarty->assign('EMAIL', $customers_email_address);
        $smarty->assign('COMMENTS', $customers_mail_comments);
        $smarty->assign('PASSWORD', $customers_password_encrypted);

        $smarty->caching = 0;
        $smarty->assign('language', $_SESSION['language']);
        $html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/admin/mail/'.$_SESSION['language'].'/create_account_mail.html');
        $txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/admin/mail/'.$_SESSION['language'].'/create_account_mail.txt');

        xtc_php_mail(EMAIL_SUPPORT_ADDRESS,
                     EMAIL_SUPPORT_NAME,
                     $customers_email_address,
                     $customers_lastname.' '.$customers_firstname,
                     EMAIL_SUPPORT_FORWARDING_STRING,
                     EMAIL_SUPPORT_REPLY_ADDRESS,
                     EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
                     '',
                     '',
                     EMAIL_SUPPORT_SUBJECT,
                     $html_mail,
                     $txt_mail);
      }
      xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID='.$cc_id, 'SSL'));
    }
  }

require (DIR_WS_INCLUDES.'head.php');
?>
</head>
<body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table class="tableBody">
      <tr>
        <?php //left_navigation
        if (USE_ADMIN_TOP_MENU == 'false') {
          echo '<td class="columnLeft2">'.PHP_EOL;
          echo '<!-- left_navigation //-->'.PHP_EOL;       
          require_once(DIR_WS_INCLUDES . 'column_left.php');
          echo '<!-- left_navigation eof //-->'.PHP_EOL; 
          echo '</td>'.PHP_EOL;      
        }
        ?>
        <!-- body_text //--> 
        <td class="boxCenter">
          <div class="pageHeadingImage"><?php echo xtc_image(DIR_WS_ICONS.'heading/icon_customers.png'); ?></div>
          <div class="pageHeading pdg2"><?php echo HEADING_TITLE; ?></div>
          <div class="clear div_box mrg5">

            <?php echo xtc_draw_form('customers', FILENAME_CREATE_ACCOUNT, xtc_get_all_get_params(array('action')) . 'action=edit', 'post', 'onSubmit="return check_form();"') . xtc_draw_hidden_field('default_address_id', isset($customers_default_address_id)?$customers_default_address_id:''); ?>

              <div class="formAreaTitle"><span class="title"><?php echo CATEGORY_PERSONAL; ?></span></div>
              <div class="formAreaC">
                <table class="tableConfig borderall">

                    <?php if (ACCOUNT_GENDER == 'true') { ?>
                      <tr>
                        <td class="dataTableConfig col-left"><?php echo ENTRY_GENDER; ?></td>
                        <td class="dataTableConfig col-single-right">
                        <?php
                          echo xtc_draw_pull_down_menu('customers_gender', get_customers_gender(), isset($customers_gender)?$customers_gender:'');
                        ?>
                        </td>                        
                      </tr>
                    <?php } ?>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_CID; ?></td>
                      <td class="dataTableConfig col-single-right bg_notice"><?php echo xtc_draw_input_field('csID', generate_customers_cid()); ?></td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_FIRST_NAME; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_firstname_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('customers_firstname', isset($customers_firstname)?$customers_firstname:'', '', true);
                        if ($error && $entry_firstname_error) echo '&nbsp;'.ENTRY_FIRST_NAME_ERROR;
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_LAST_NAME; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_lastname_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('customers_lastname', isset($customers_lastname)?$customers_lastname:'', '', true);
                        if ($error && $entry_lastname_error) echo '&nbsp;'.ENTRY_LAST_NAME_ERROR;
                      ?>
                      </td>
                    </tr>

                    <?php if (ACCOUNT_DOB == 'true') { ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_date_of_birth_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('customers_dob', xtc_date_short(isset($customers_dob)?$customers_dob:''), '', true);
                        if ($error && $entry_date_of_birth_error) echo '&nbsp;'.ENTRY_DATE_OF_BIRTH_ERROR;
                      ?>
                      </td>
                    </tr>
                    <?php } ?>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && ($entry_email_address_error == true || $entry_email_address_check_error == true || $entry_email_address_exists == true)) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('customers_email_address', isset($customers_email_address)?$customers_email_address:'', '', true);
                        if ($error && $entry_email_address_error) echo '&nbsp;'.ENTRY_EMAIL_ADDRESS_ERROR;
                        if ($error && $entry_email_address_check_error) echo '&nbsp;'.ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
                        if ($error && $entry_email_address_exists) echo '&nbsp;'.ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
                      ?>
                      </td>
                    </tr>

                  </table>
                </div>


                <?php if (ACCOUNT_COMPANY == 'true') { ?>
                <div class="formAreaTitle"><span class="title"><?php echo CATEGORY_COMPANY; ?></span></div>        
                <div class="formAreaC">
                  <table class="tableConfig borderall">

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_COMPANY; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        echo xtc_draw_input_field('entry_company', isset($entry_company)?$entry_company:'');
                      ?>
                      </td>
                    </tr>

                    <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') { ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_VAT_ID; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        echo xtc_draw_input_field('customers_vat_id', isset($customers_vat_id)?$customers_vat_id:'');
                        if ($error && $entry_vat_error_text) echo '&nbsp;' . $entry_vat_error_text;
                      ?>
                      </td>
                    </tr>
                    <?php } ?>

                  </table>
                </div>
                <?php } ?>


                <div class="formAreaTitle"><span class="title"><?php echo CATEGORY_ADDRESS; ?></span></div>        
                <div class="formAreaC">
                  <table class="tableConfig borderall">

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_STREET_ADDRESS; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_street_address_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('entry_street_address', isset($entry_street_address)?$entry_street_address:'', '', true);
                        if ($error && $entry_street_address_error) echo '&nbsp;'.ENTRY_STREET_ADDRESS_ERROR;
                      ?>
                      </td>
                    </tr>

                    <?php if (ACCOUNT_SUBURB == 'true') { ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_SUBURB; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        echo xtc_draw_input_field('entry_suburb', isset($entry_suburb)?$entry_suburb:'');
                      ?>
                      </td>
                    </tr>
                    <?php } ?>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_POST_CODE; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_post_code_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('entry_postcode', isset($entry_postcode)?$entry_postcode:'', '', true);
                        if ($error && $entry_post_code_error) echo '&nbsp;'.ENTRY_POST_CODE_ERROR;
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_CITY; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_city_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('entry_city', isset($entry_city)?$entry_city:'', '', true);
                        if ($error && $entry_city_error) echo '&nbsp;'.ENTRY_CITY_ERROR;
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_COUNTRY; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_country_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(xtc_get_country_name(STORE_COUNTRY), '1'), isset($entry_country_id)?$entry_country_id:'');
                        if ($error && $entry_country_error) echo '&nbsp;'.ENTRY_COUNTRY_ERROR;
                      ?>
                      </td>
                    </tr>
                    
                    <?php if (ACCOUNT_STATE == 'true') { ?>
                    <tr id="states">
                      <td class="dataTableConfig col-left"><?php echo ENTRY_STATE; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_state_error == true) ? ' col-error' : ''); ?>" id="entry_state">
                      <?php                      
                        $entry_state = xtc_get_zone_code(isset($entry_country_id) ? $entry_country_id : xtc_get_countries(xtc_get_country_name(STORE_COUNTRY)), isset($entry_zone_id) ? $entry_zone_id :'', isset($entry_state) ? $entry_state:'');
                        $entry_state_str = xtc_draw_input_field('entry_state', $entry_state);
                        if ($error && $entry_state_error) {
                          if ($entry_state_has_zones) {
                            $zones_array = array ();
                            $zones_query = xtc_db_query("SELECT zone_code, zone_name FROM ".TABLE_ZONES." WHERE zone_country_id = '".xtc_db_input($entry_country_id)."' ORDER BY zone_name");
                            while ($zones_values = xtc_db_fetch_array($zones_query)) $zones_array[] = array ('id' => $zones_values['zone_code'], 'text' => $zones_values['zone_name']);
                            $entry_state_str = xtc_draw_pull_down_menu('entry_state', $zones_array, $entry_state).'&nbsp;'.ENTRY_STATE_ERROR;
                          } else {
                            $entry_state_str .= '&nbsp;'.ENTRY_STATE_ERROR;
                          }
                        }
                        echo $entry_state_str;
                      ?>
                      </td>
                    </tr>
                    <?php } ?>

                  </table>
                </div>


                <div class="formAreaTitle"><span class="title"><?php echo CATEGORY_CONTACT; ?></span></div>
                <div class="formAreaC">
                  <table class="tableConfig borderall">
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
                      <td class="dataTableConfig col-single-right<?php echo (($error == true && $entry_telephone_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_input_field('customers_telephone', isset($customers_telephone)?$customers_telephone:'', '', (ACCOUNT_TELEPHONE_OPTIONAL == 'false'));
                        if ($error && $entry_telephone_error) echo '&nbsp;'.ENTRY_TELEPHONE_NUMBER_ERROR;
                      ?>
                      </td>
                    </tr>

                    <?php if (ACCOUNT_FAX == 'true') { ?>
                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_FAX_NUMBER; ?></td>
                      <td class="dataTableConfig col-single-right"><?php echo xtc_draw_input_field('customers_fax'); ?></td>
                    </tr>
                    <?php } ?>

                  </table>
                </div>
                
                <div class="formAreaTitle"><span class="title"><?php echo CATEGORY_OPTIONS; ?></span></div>        
                <div class="formAreaC">
                  <table class="tableConfig borderall">

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        if (isset($processed) && $processed == true) {
                          echo xtc_draw_hidden_field('status');
                        } else {                         
                          echo xtc_draw_pull_down_menu('status', $customers_statuses_array, DEFAULT_CUSTOMERS_STATUS_ID, 'style="width:145px;"');
                        }
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_MAIL; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        echo '<label>'.xtc_draw_radio_field('customers_mail', 'yes', true, isset($customers_send_mail)?$customers_send_mail:'').'&nbsp;&nbsp;'.YES.'</label>&nbsp;&nbsp;';
                        echo '<label>'.xtc_draw_radio_field('customers_mail', 'no', false, isset($customers_send_mail)?$customers_send_mail:'').'&nbsp;&nbsp;'.NO.'</label>';
                        if ($error && $entry_mail_error) echo '&nbsp;'.ENTRY_MAIL_ERROR;
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        $customers_payment_unallowed = array();
                        $payment_unallowed = explode(',', isset($payment_unallowed) ? $payment_unallowed : '');
                        foreach ($payment_unallowed as $value) {
                          $customers_payment_unallowed[] = $value;
                        }
                        if (xtc_not_null(MODULE_PAYMENT_INSTALLED)) {
                          $payment_status = explode(';', MODULE_PAYMENT_INSTALLED);
                          for ($p=0, $x=sizeof($payment_status); $p<$x; $p++) {
                            if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $payment_status[$p])) {
                              include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $payment_status[$p]);
                            }
                            echo xtc_draw_checkbox_field('payment_unallowed[]', substr($payment_status[$p], 0,-4), (in_array(substr($payment_status[$p], 0,-4), $customers_payment_unallowed) ? true : false)).constant('MODULE_PAYMENT_'.strtoupper(substr($payment_status[$p], 0,-4)).'_TEXT_TITLE').' ('.$payment_status[$p].')<br/>';
                          }
                        } else {
                          echo TEXT_PAYMENT_ERROR;
                        }
                      ?>                     
                      </td>                     
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></td>
                      <td class="dataTableConfig col-single-right">
                      <?php
                        $customers_shipping_unallowed = array();
                        $shipping_unallowed = explode(',', isset($shipping_unallowed) ? $shipping_unallowed : '');
                        foreach ($shipping_unallowed as $value) {
                          $customers_shipping_unallowed[] = $value;
                        }
                        if (xtc_not_null(MODULE_SHIPPING_INSTALLED)) {
                          $shipping_status = explode(';', MODULE_SHIPPING_INSTALLED);
                          for ($s=0, $x=sizeof($shipping_status); $s<$x; $s++) {
                            if (is_file(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/shipping/' . $shipping_status[$s])) {
                              include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/shipping/' . $shipping_status[$s]);
                            }
                            echo xtc_draw_checkbox_field('shipping_unallowed[]', substr($shipping_status[$s], 0,-4), (in_array(substr($shipping_status[$s], 0,-4), $customers_shipping_unallowed) ? true : false)).constant('MODULE_SHIPPING_'.strtoupper(substr($shipping_status[$s], 0,-4)).'_TEXT_TITLE').' ('.$shipping_status[$s].')<br/>';
                          }
                        } else {
                          echo TEXT_SHIPPING_ERROR;
                        }
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_PASSWORD; ?></td>
                      <td class="dataTableConfig col-single-right bg_notice<?php echo (($error == true && $entry_password_error == true) ? ' col-error' : ''); ?>">
                      <?php
                        echo xtc_draw_password_field('entry_password', isset($customers_password_encrypted)?$customers_password_encrypted:'', true, 'autocomplete="off" readonly="readonly" onfocus="this.removeAttribute(\'readonly\');" onblur="this.setAttribute(\'readonly\', \'readonly\');"');
                        if ($error && $entry_password_error) echo '&nbsp;'.ENTRY_PASSWORD_ERROR;
                      ?>
                      </td>
                    </tr>

                    <tr>
                      <td class="dataTableConfig col-left"><?php echo ENTRY_MAIL_COMMENTS; ?></td>
                      <td class="dataTableConfig col-single-right"><?php echo xtc_draw_textarea_field('mail_comments', 'soft', '60', '5', isset($mail_comments)?$mail_comments:''); ?></td>
                    </tr>

                  </table>
                </div>
             
                <div class="main mrg5">
                <?php
                  echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_INSERT . '">';
                  echo '<a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS, xtc_get_all_get_params(array('action'))) .'">' . BUTTON_CANCEL . '</a>';
                ?>
                </div>

              </form>
            </div> 
        </td>
        <!-- body_text_eof //-->
      </tr>
    </table>
    <?php require(DIR_WS_INCLUDES . 'javascript/jquery.entry_state.js.php'); ?>
    <script>
      $(document).ready(function () {
        create_states($('select[name="entry_country_id"]').val(), 'entry_state');
    
        $('select[name="entry_country_id"]').change(function() {
          create_states($(this).val(), 'entry_state');
        });
      });
    </script>
    <!-- body_eof //-->
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>