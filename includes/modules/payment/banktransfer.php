<?php
/* -----------------------------------------------------------------------------------------
   $Id: banktransfer.php 16231 2024-12-04 15:54:51Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banktransfer.php,v 1.16 2003/03/02 22:01:50); www.oscommerce.com
   (c) 2003 nextcommerce (banktransfer.php,v 1.9 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce (banktransfer.php 1122 2005-07-26)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   OSC German Banktransfer v0.85a         Autor:  Dominik Guder <osc@guder.org>

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  // include needed classes
  require_once(DIR_FS_CATALOG.'includes/classes/modified_api.php');


  class banktransfer {
  
    var $code;
    var $title;
    var $info;
    var $description;
    var $extended_description;
    var $sort_order;
    var $enabled;
    var $properties;
    var $min_order;
    var $order_status;
    var $email_footer;
    var $_check;

    var $iban_mode;
    var $banktransfer_bankname;
    var $banktransfer_owner;
    var $banktransfer_owner_email;
    var $banktransfer_iban;
    var $banktransfer_bic;
    var $banktransfer_number;
    var $banktransfer_blz;
    var $banktransfer_prz;
    var $banktransfer_status;
    var $banktransfer_fax;

    function __construct() {
      global $order;
      
      $this->install_update();
      
      $this->code = 'banktransfer';
      $this->title = MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE;
      $this->info = MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO;
      $this->description = MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION;
      //$this->extended_description = MODULE_PAYMENT_BANKTRANSFER_TEXT_EXTENDED_DESCRIPTION;
      $this->sort_order = ((defined('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER')) ? MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER : '');
      $this->enabled = ((defined('MODULE_PAYMENT_BANKTRANSFER_STATUS') && MODULE_PAYMENT_BANKTRANSFER_STATUS == 'True') ? true : false);      

      $this->properties['button_update'] = '<a class="button btnbox" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=payment&action=update&module='.$this->code) . '">' . BUTTON_UPDATE . '</a>';

      if ($this->check() > 0) {
        $this->min_order = MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER;
        if ((int)MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID > 0) {
          $this->order_status = MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID;
        }
        if (isset($_POST['banktransfer_fax']) && $_POST['banktransfer_fax'] == "on") {
          $this->email_footer = MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER;
        }
      }
      
      if (!defined('RUN_MODE_ADMIN') && is_object($order)) {
        $this->update_status();
      }
    }

    function update_status() {
      global $order;

      $check_order_query = xtc_db_query("SELECT COUNT(*) as count 
                                           FROM ".TABLE_ORDERS." 
                                          WHERE customers_id = '".(int) $_SESSION['customer_id']."' 
                                            AND orders_status IN (".MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_STATUS_ID.")");
      $order_check = xtc_db_fetch_array($check_order_query);

      if ($order_check['count'] < MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER) {
        $check_flag = false;
        $this->enabled = false;
      } else {
        $check_flag = true;
        if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_BANKTRANSFER_ZONE > 0) ) {
          $check_flag = false;
          $check_query = xtc_db_query("SELECT zone_id 
                                         FROM ".TABLE_ZONES_TO_GEO_ZONES." 
                                        WHERE geo_zone_id = '".(int)MODULE_PAYMENT_BANKTRANSFER_ZONE."' 
                                          AND zone_country_id = '".(int)$order->billing['country']['id']."' 
                                     ORDER BY zone_id");
          while ($check = xtc_db_fetch_array($check_query)) {
            if ($check['zone_id'] < 1) {
              $check_flag = true;
              break;
            } elseif ($check['zone_id'] == $order->billing['zone_id']) {
              $check_flag = true;
              break;
            }
          }
        }
        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      $js = 'if (payment_value == "' . $this->code . '") {' . "\n" .
            '  var banktransfer_blz = document.getElementById("checkout_payment").banktransfer_blz.value;' . "\n" .
            '  var banktransfer_number = document.getElementById("checkout_payment").banktransfer_number.value;' . "\n" .
            '  var banktransfer_owner = document.getElementById("checkout_payment").banktransfer_owner.value;' . "\n" .
            '  var banktransfer_owner_email = document.getElementById("checkout_payment").banktransfer_owner_email.value;' . "\n" .
            '  if (document.getElementById("checkout_payment").banktransfer_fax) { ' . "\n" .
            '    var banktransfer_fax = document.getElementById("checkout_payment").banktransfer_fax.checked;' . "\n" .
            '  } else { var banktransfer_fax = false; } ' . "\n" .
            '  if (banktransfer_fax == false) {' . "\n" .
            '    if (banktransfer_number.substr(0, 2) != "DE" && banktransfer_blz == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_BLZ . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (banktransfer_number == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (banktransfer_owner == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (banktransfer_owner_email == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_OWNER_EMAIL . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n" .
            '}' . "\n";
      
      return $js;
    }

    function selection() {
      global $order;
            
      $selection = array(
        'id' => $this->code,
        'module' => $this->title,
        'description'=>$this->info,
        'fields' => array(
          array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE,
                'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO),
          array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER,
                'field' => xtc_draw_input_field('banktransfer_owner', ((isset($_SESSION['banktransfer_info']['banktransfer_owner'])) ? $_SESSION['banktransfer_info']['banktransfer_owner'] : $order->billing['firstname'] . ' ' . $order->billing['lastname']), 'size="40" maxlength="64"') . ((isset($_POST['recheckok'])) ? xtc_draw_hidden_field('recheckok', $_POST['recheckok']) : '')),
          array('title' => ((MODULE_PAYMENT_BANKTRANSFER_IBAN_ONLY == 'False') ? MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER : MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_IBAN),
                'field' => xtc_draw_input_field('banktransfer_number', ((isset($_SESSION['banktransfer_info']['banktransfer_number'])) ? $_SESSION['banktransfer_info']['banktransfer_number'] : ''), 'size="40" maxlength="40"')),
          array('title' => ((MODULE_PAYMENT_BANKTRANSFER_IBAN_ONLY == 'False') ? MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ : MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BIC),
                'field' => xtc_draw_input_field('banktransfer_blz', ((isset($_SESSION['banktransfer_info']['banktransfer_blz'])) ? $_SESSION['banktransfer_info']['banktransfer_blz'] : ''), 'size="40" maxlength="11"')),
          array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME,
                'field' => xtc_draw_input_field('banktransfer_bankname', ((isset($_SESSION['banktransfer_info']['banktransfer_bankname'])) ? $_SESSION['banktransfer_info']['banktransfer_bankname'] : ''), 'size="40" maxlength="64"')),
          array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER_EMAIL,
                'field' => xtc_draw_input_field('banktransfer_owner_email', ((isset($_SESSION['banktransfer_info']['banktransfer_owner_email'])) ? $_SESSION['banktransfer_info']['banktransfer_owner_email'] : $order->customer['email_address']), 'size="40" maxlength="96"')),
        )
      );

      if (MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION =='true') {
        $selection['fields'][] = array(
          'title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE,
          'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2 . '<a href="' . MODULE_PAYMENT_BANKTRANSFER_URL_NOTE . '" target="_blank"><b>' . MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3 . '</b></a>' . MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4
        );
        $selection['fields'][] = array(
          'title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX,
          'field' => xtc_draw_checkbox_field('banktransfer_fax', 'on')
        );
      }
      
      return $selection;
    }

    function pre_confirmation_check() {
      global $messageStack;
      
      if (!isset($_POST['banktransfer_fax']) && (!isset($_POST['recheckok']) || $_POST['recheckok'] != 'true')) {
        include(DIR_WS_CLASSES . 'banktransfer_validation.php');

        // iban / classic?
        $number = preg_replace('/[^a-zA-Z0-9]/', '', ((isset($_POST['banktransfer_iban'])) ? $_POST['banktransfer_iban'] : $_POST['banktransfer_number']));
        if (ctype_digit($number) && MODULE_PAYMENT_BANKTRANSFER_IBAN_ONLY == 'False') {
          // classic
          $banktransfer_validation = new AccountCheck;
          $banktransfer_result = $banktransfer_validation->CheckAccount($number, $_POST['banktransfer_blz']);
          // some error codes <> 0/OK pass as OK 
          if ($banktransfer_validation->account_acceptable($banktransfer_result))
            $banktransfer_result = 0;
        } else {
          // iban
          $banktransfer_validation = new IbanAccountCheck;
          $banktransfer_result = $banktransfer_validation->IbanCheckAccount($number, ((isset($_POST['banktransfer_bic'])) ? $_POST['banktransfer_bic'] : $_POST['banktransfer_blz']));
          // some error codes <> 0/OK pass as OK
          if ($banktransfer_validation->account_acceptable($banktransfer_result))
            $banktransfer_result = 0;
          // owner email ?
          if ($banktransfer_result == 0 && isset($_POST['banktransfer_owner_email'])) {
            require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');
            if (!xtc_validate_email($_POST['banktransfer_owner_email']))
              $banktransfer_result = 13;
          }  
          // iban country allowed in payment zone?
          if ($banktransfer_result == 0 && ((int)MODULE_PAYMENT_BANKTRANSFER_ZONE > 0)) {
            $check_query = xtc_db_query("SELECT DISTINCT z.geo_zone_id 
                                                    FROM ".TABLE_ZONES_TO_GEO_ZONES." z
                                                    JOIN ".TABLE_COUNTRIES." c 
                                                         ON c.countries_id = z.zone_country_id
                                                            AND c.countries_iso_code_2 = '".xtc_db_input($banktransfer_validation->IBAN_country)."'
                                                   WHERE z.geo_zone_id = '".(int)MODULE_PAYMENT_BANKTRANSFER_ZONE."'");
            if (xtc_db_num_rows($check_query) == 0)
              $banktransfer_result = 14;
          }
          
          // map return codes. refine where necessary
          // iban not ok
          if (in_array($banktransfer_result, array(1000, 1010, 1020, 1030, 1040))) 
            $banktransfer_result = 12;
          // bic not ok
          else if (in_array($banktransfer_result, array(1050, 1060, 1070, 1080))) 
            $banktransfer_result = 11;
          // classic check of bank details derived from iban, map to classic return codes
          else if ($banktransfer_result > 2000) 
            $banktransfer_result -= 2000;
          
        } 
        
        if (!empty($banktransfer_validation->Bankname)) {
          $this->banktransfer_bankname =  $banktransfer_validation->Bankname;
        } else {
          $this->banktransfer_bankname = xtc_db_prepare_input($_POST['banktransfer_bankname']);
        }
        if (isset($_POST['banktransfer_owner']) && $_POST['banktransfer_owner'] == '') {
          $banktransfer_result = 10;
        }

        switch ($banktransfer_result) {
          case 0: // payment o.k.
            $error = 'O.K.';
            $recheckok = 'false';
            break;
          case 1: // number & blz not ok
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1;
            $recheckok = 'false';
            break;
          case 2: // account number has no calculation method
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2;
            $recheckok = 'true';
            break;
          case 3: // No calculation method implemented
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3;
            $recheckok = 'true';
            break;
          case 4: // Number cannot be checked
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4;
            $recheckok = 'true';
            break;
          case 5: // BLZ not found
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5;
            $recheckok = 'false'; // Set "true" if you have not the latest BLZ table!
            break;
          case 8: // no BLZ entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8;
            $recheckok = 'false';
            break;
          case 9: // no number entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9;
            $recheckok = 'false';
            break;
          case 10: // no account holder entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10;
            $recheckok = 'false';
            break;
          case 11: // no bic entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_11;
            $recheckok = 'false';
            break;
          case 12: // iban not o.k.
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_12;
            $recheckok = 'false';
            break;
          case 13: // no account holder notification email entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_13;
            $recheckok = 'false';
            break;
          case 14: // iban country not allowed in payment zone
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_14;
            $recheckok = 'false';
            break;
          case 128: // Internal error
            $error = 'Internal error, please check again to process your payment';
            $recheckok = 'true';
            break;
          default:
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4;
            $recheckok = 'true';
            break;
        }

        if ($banktransfer_result > 0 && (!isset($_POST['recheckok']) || $_POST['recheckok'] != 'true')) {
          $messageStack->add_session('banktransfer', $error);
          $_SESSION['banktransfer_info'] = $_POST;

          xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&recheckok=' . $recheckok, 'SSL'));
        }
        
        $this->iban_mode = ($banktransfer_validation->checkmode == 'iban');
        $this->banktransfer_owner = xtc_db_prepare_input($_POST['banktransfer_owner']);
        $this->banktransfer_owner_email = xtc_db_prepare_input($_POST['banktransfer_owner_email']);
        $this->banktransfer_iban = $banktransfer_validation->banktransfer_iban;
        $this->banktransfer_bic = $banktransfer_validation->banktransfer_bic;
        $this->banktransfer_number = $banktransfer_validation->banktransfer_number;
        $this->banktransfer_blz = $banktransfer_validation->banktransfer_blz;
        $this->banktransfer_prz = $banktransfer_validation->PRZ;
        $this->banktransfer_status = $banktransfer_result;
      }
    }

    function confirmation() {
      // write data so session      
      $_SESSION['banktransfer_info'] = array(
        'banktransfer_owner' => $this->banktransfer_owner,
        'banktransfer_owner_email' => $this->banktransfer_owner_email,
        'banktransfer_bankname' => $this->banktransfer_bankname,
        'banktransfer_number' => (($this->iban_mode) ? $this->banktransfer_iban : $this->banktransfer_number),
        'banktransfer_blz' => (($this->iban_mode) ? $this->banktransfer_bic : $this->banktransfer_blz),
      );
             
      if ($_POST['banktransfer_owner'] != '') {
        $confirmation = array(
          'title' => $this->title,
          'fields' => array(
            array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER,
                  'field' => $this->banktransfer_owner),
            array('title' => (($this->iban_mode) ? MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_IBAN : MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER),
                  'field' => (($this->iban_mode) ? $this->banktransfer_iban : $this->banktransfer_number)),
            array('title' => (($this->iban_mode) ? MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BIC : MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ),
                  'field' => (($this->iban_mode) ? $this->banktransfer_bic : $this->banktransfer_blz)),
            array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME,
                  'field' => $this->banktransfer_bankname),
            array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER_EMAIL,
                  'field' => $this->banktransfer_owner_email),
          )
        );
      }
      
      if (isset($_POST['banktransfer_fax']) && $_POST['banktransfer_fax'] == "on") {
        $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX)));
        $this->banktransfer_fax = "on";
      }
      
      return $confirmation;
    }

    function process_button() {      
      $process_button_string = xtc_draw_hidden_field('banktransfer_blz', $this->banktransfer_blz) .
                               xtc_draw_hidden_field('banktransfer_bankname', $this->banktransfer_bankname).
                               xtc_draw_hidden_field('banktransfer_number', $this->banktransfer_number) .
                               xtc_draw_hidden_field('banktransfer_iban', $this->banktransfer_iban) .
                               xtc_draw_hidden_field('banktransfer_bic', $this->banktransfer_bic) .
                               xtc_draw_hidden_field('banktransfer_owner', $this->banktransfer_owner) .
                               xtc_draw_hidden_field('banktransfer_owner_email', $this->banktransfer_owner_email) .
                               xtc_draw_hidden_field('banktransfer_status', $this->banktransfer_status) .
                               xtc_draw_hidden_field('banktransfer_prz', $this->banktransfer_prz) .
                               (isset($_POST['banktransfer_fax'])? xtc_draw_hidden_field('banktransfer_fax', $this->banktransfer_fax):'');

      return $process_button_string;
    }

    function before_process() {
      $this->pre_confirmation_check();
      
      return false;
    }

    function before_send_order() {
      global $insert_id;
      
      $sql_data_array = array(
        'orders_id' => $insert_id,
        'banktransfer_owner' => xtc_db_prepare_input($_POST['banktransfer_owner']),
        'banktransfer_number' => xtc_db_prepare_input($_POST['banktransfer_number']),
        'banktransfer_bankname' => xtc_db_prepare_input($_POST['banktransfer_bankname']),
        'banktransfer_blz' => xtc_db_prepare_input($_POST['banktransfer_blz']),
        'banktransfer_status' => xtc_db_prepare_input($_POST['banktransfer_status']),
        'banktransfer_prz' => xtc_db_prepare_input($_POST['banktransfer_prz']),
        'banktransfer_iban' => xtc_db_prepare_input($_POST['banktransfer_iban']),
        'banktransfer_bic' => xtc_db_prepare_input($_POST['banktransfer_bic']),
        'banktransfer_owner_email' => xtc_db_prepare_input($_POST['banktransfer_owner_email']),
      );
      if (isset($_POST['banktransfer_fax'])) {
        $sql_data_array['banktransfer_fax'] = xtc_db_prepare_input($_POST['banktransfer_fax']);
      }
      xtc_db_perform(TABLE_BANKTRANSFER, $sql_data_array);
      
      if (isset($this->order_status) && $this->order_status) {
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status = ".$this->order_status." WHERE orders_id = ".$insert_id);
        xtc_db_query("UPDATE ".TABLE_ORDERS_STATUS_HISTORY." SET orders_status_id = ".$this->order_status." WHERE orders_id = ".$insert_id);
      }
      
      unset($_SESSION['banktransfer_info']);
    }
    
    function success() {
      global $insert_id, $last_order, $xtPrice, $PHP_SELF;
             
      $insert_id = $last_order;
      $banktransfer_data = $this->info();

      if (!empty($banktransfer_data['banktransfer_iban'])) {
        require_once (DIR_FS_INC.'xtc_date_short.inc.php');
        require_once (DIR_FS_INC.'get_order_total.inc.php');
        
        $smarty = new Smarty();
        $smarty->caching = 0;
        $smarty->assign('language', $_SESSION['language']);
        $smarty->assign('PAYMENT_BANKTRANSFER_CREDITOR_ID', MODULE_PAYMENT_BANKTRANSFER_CI);
        $smarty->assign('PAYMENT_BANKTRANSFER_DUE_DATE',  xtc_date_short(date('Y-m-d', strtotime(' + ' . MODULE_PAYMENT_BANKTRANSFER_DUE_DELAY . ' days'))));     
        $smarty->assign('PAYMENT_BANKTRANSFER_TOTAL', $xtPrice->xtcFormat(get_order_total($insert_id), true));
        $smarty->assign('PAYMENT_BANKTRANSFER_MANDATE_REFERENCE', MODULE_PAYMENT_BANKTRANSFER_REFERENCE_PREFIX . $insert_id);
        $smarty->assign('PAYMENT_BANKTRANSFER_IBAN', substr($banktransfer_data['banktransfer_iban'], 0, 8) . str_repeat('*', (strlen($banktransfer_data['banktransfer_iban']) - 10)) . substr($banktransfer_data['banktransfer_iban'], -2));
        $smarty->assign('PAYMENT_BANKTRANSFER_BANKNAME', $banktransfer_data['banktransfer_bankname']);
      
        $sepa_info = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/sepa_info.html');
        
        $success = array(
          array(
            'title' => ((basename($PHP_SELF) == FILENAME_CHECKOUT_SUCCESS) ? $this->title : ''),
            'class' => $this->code,
            'fields' => array(
              array('title' => '',
                    'field' => $sepa_info),
            )
          )
        );

        return $success;
      }
    }

    function info() {
      global $insert_id;
      
      if (isset($insert_id)) {
        $banktransfer_query = xtc_db_query("SELECT banktransfer_iban,
                                                   banktransfer_bankname,
                                                   banktransfer_owner,
                                                   banktransfer_owner_email
                                              FROM ".TABLE_BANKTRANSFER."
                                             WHERE orders_id = ".(int)$insert_id);
        if (xtc_db_num_rows($banktransfer_query) > 0) {
          $banktransfer = xtc_db_fetch_array($banktransfer_query);
          return $banktransfer;
        }
      }
      
      return array(
        'banktransfer_owner' => $this->banktransfer_owner,
        'banktransfer_owner_email' => $this->banktransfer_owner_email,
        'banktransfer_bankname' => $this->banktransfer_bankname,
        'banktransfer_iban' => $this->banktransfer_iban, 
      );
    }
    
    function get_error() {
      global $messageStack;
      
      if ($messageStack->size('banktransfer') > 0) {
        $error = array(
          'title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR,
          'error_message' => $messageStack->output('banktransfer'),
        );
        
        return $error;
      }    
    }

    function check() {
      if (!isset($this->_check)) {
        if (defined('MODULE_PAYMENT_BANKTRANSFER_STATUS')) {
          $this->_check = true;
        } else {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BANKTRANSFER_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
      }
      return $this->_check;
    }

    function update() {
      global $messageStack;
      
      $filename = DIR_FS_CATALOG.'cache/blz.txt';
      
      modified_api::reset();
      $response = modified_api::request('bundesbank/blz');
      
      if ($response != null && is_array($response) && isset($response['requestURL'])) {
        // include needed functions
        require_once (DIR_FS_INC.'get_external_content.inc.php');

        $blz_file_content = get_external_content($response['requestURL'], 3, false);
        file_put_contents($filename, $blz_file_content);
      }

      if (is_file($filename)) {
        if (($handle = fopen($filename, "r")) !== false) {
          xtc_db_query("TRUNCATE ".TABLE_BANKTRANSFER_BLZ);
          
          $cnt = 0;
          while (!feof($handle)) {
            $line = stream_get_line($handle, 65535, "\n");
            $kennzeichen = substr($line, 158, 1);
                                
            if ($kennzeichen != 'D'
                && substr($line, 8, 1) == '1'
                )
            {
              $sql_data_array = array(
                'blz' => substr($line, 0, 8),
                'bankname' => encode_utf8(trim(substr($line, 9, 58))),
                'prz' => substr($line, 150, 2),
              );
              
              xtc_db_perform(TABLE_BANKTRANSFER_BLZ, $sql_data_array);
              $cnt ++;
            }
          }
          fclose($handle);
        }
        $messageStack->add_session(MODULE_PAYMENT_BANKTRANSFER_TEXT_UPDATE_SUCCESS.$cnt, 'success');
        unlink($filename);
      } else {
        $messageStack->add_session(MODULE_PAYMENT_BANKTRANSFER_TEXT_UPDATE_ERROR, 'error');
      }
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_display_orders_statuses', 'xtc_cfg_multi_checkbox(\'xtc_get_orders_status\', \'chr(44)\',', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION', 'false',  '6', '2', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ', 'false', '6', '0', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE', 'fax.html', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_CI', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_REFERENCE_PREFIX', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_DUE_DELAY', '1', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_IBAN_ONLY', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }
    
    function install_update() {
      if (!defined('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_STATUS_ID')) {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_display_orders_statuses', 'xtc_cfg_multi_checkbox(\'xtc_get_orders_status\', \'chr(44)\',', now())");
        define('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_STATUS_ID', '0');
      }
    }
    
    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array(
        'MODULE_PAYMENT_BANKTRANSFER_STATUS',
        'MODULE_PAYMENT_BANKTRANSFER_ALLOWED',
        'MODULE_PAYMENT_BANKTRANSFER_ZONE',
        'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID',
        'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER',
        'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ',
        'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION',
        'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER',
        'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_STATUS_ID',
        'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE',
        'MODULE_PAYMENT_BANKTRANSFER_CI',
        'MODULE_PAYMENT_BANKTRANSFER_REFERENCE_PREFIX',
        'MODULE_PAYMENT_BANKTRANSFER_DUE_DELAY',
        'MODULE_PAYMENT_BANKTRANSFER_IBAN_ONLY',
      );
    }
  }
