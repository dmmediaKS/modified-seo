<?php
/* -----------------------------------------------------------------------------------------
   $Id: sofort_ideal.php 15761 2024-02-29 18:59:48Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
 	 based on:
	  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
	  (c) 2002-2003 osCommerce - www.oscommerce.com
	  (c) 2001-2003 TheMedia, Dipl.-Ing Thomas Pl�nkers - http://www.themedia.at & http://www.oscommerce.at
	  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com
    (c) 2010 Payment Network AG - http://www.payment-network.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include autoloader
require_once(DIR_FS_EXTERNAL.'sofort/autoload.php');

// include needed classes
require_once(DIR_FS_EXTERNAL.'sofort/classes/sofortLibIdeal.inc.php');
require_once(DIR_FS_EXTERNAL.'sofort/classes/SofortLibPayment.php');

class sofort_ideal extends SofortLibPayment {

	var $code;
	var $tmpOrders;
	var $version;

	var $ideal;
	var $logger;
	var $logging;
	var $data;
	var $SofortIdeal;

  function __construct() {
    $this->SofortPayment();

    // set variable
    $this->ideal = true;

    // logger
    $this->logger = new Sofort\SofortLib\FileLogger();
    $this->logger->setLogfilePath(DIR_FS_LOG.'sofort_'.date('Y-m-d').'.log');
	}


	function pre_confirmation_check() {
	  global $messageStack;
	  
	  if (!isset($_POST['ideal_bank_name']) || $_POST['ideal_bank_name'] == '0') {
	    $messageStack->add_session('checkout_payment', constant('MODULE_PAYMENT_'.strtoupper($this->code).'_SELECTBOX'));
      xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	  }
	}


	function process_button() {
		return xtc_draw_hidden_field('ideal_bank_name', $_POST['ideal_bank_name']);
	}


  function _payment_action () {
    global $order, $xtPrice, $insert_id;

    if (!isset($insert_id) || $insert_id == '') {
      $insert_id = $_SESSION['tmp_oID'];
    }

    $this->_payment_data();

    // prepare call
    $this->SofortIdeal = new SofortLibIdeal(constant('MODULE_PAYMENT_'.strtoupper($this->code).'_KEY'),
                                            constant('MODULE_PAYMENT_'.strtoupper($this->code).'_PROJECT_PASS'),
                                            constant('MODULE_PAYMENT_'.strtoupper($this->code).'_HASH_ALGORITHM'));

    // set Logging
    $this->SofortIdeal->setLogger($this->logger);
    if ($this->logging === true) {
      $this->SofortIdeal->setLogEnabled();
    }

    $this->SofortIdeal->setAmount($this->data['amount']);
    $this->SofortIdeal->setCurrencyCode($this->data['currency']);
    $this->SofortIdeal->setReason($this->shortenReason($this->data['reason_1']), $this->shortenReason($this->data['reason_2']));
    $this->SofortIdeal->setOrderID($insert_id);
    $this->SofortIdeal->setCustomerID($_SESSION['customer_id']);
    $this->SofortIdeal->setCallbackIdentifier(32);
    $this->SofortIdeal->setSuccessUrl($this->data['success_url'], true);
    $this->SofortIdeal->setAbortUrl($this->data['abort_url']);
    $this->SofortIdeal->setNotificationUrl($this->data['callback_url']);
    $this->SofortIdeal->setEncoding($_SESSION['language_charset']);
    $this->SofortIdeal->setVersion($this->version);

    $this->SofortIdeal->setSenderBankCode($_POST['ideal_bank_name']);
    $this->SofortIdeal->setSenderCountryId();

		$paymentUrl = $this->SofortIdeal->getPaymentUrl();

    // write some variables to session
    $_SESSION['sofort'][$this->code]['total'] = $this->data['amount'];
    $_SESSION['sofort'][$this->code]['cartID'] = $_SESSION['cart']->cartID;
    $_SESSION['sofort'][$this->code]['oID'] = $insert_id;
    $_SESSION['sofort'][$this->code]['tID'] = $this->SofortIdeal->getUserVariable(2);

    if ($this->tmpOrders === true) {
      // update status
      $order_status_id = constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TMP_STATUS_ID');
      if ($order_status_id < 0) {
        $order_status_id = $this->_get_orders_status($insert_id);
      }
      $this->update_sofort_status($insert_id, $order_status_id, '', true);
    }

    // redirected to $paymentUrl
    xtc_redirect($paymentUrl);
  }


	function install () {
	  $this->install_default();
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_".strtoupper($this->code)."_KEY', '',  '6', '4', now())");
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_".strtoupper($this->code)."_PROJECT_PASS', '',  '6', '4', now())");
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_".strtoupper($this->code)."_NOTIFY_PASS', '',  '6', '4', now())");
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_".strtoupper($this->code)."_HASH_ALGORITHM', 'sha1',  '6', '6', 'xtc_cfg_select_option(array(\'md5\',\'sha1\',\'sha256\',\'sha512\'), ', now())");
	}


	function remove () {
		xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
	  xtc_db_query("DROP TABLE `".$this->code."`");
	}


	function keys () {
	  $keys = $this->keys_default();
	  $keys[1] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_KEY';
	  $keys[2] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_PROJECT_PASS';
	  $keys[3] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_NOTIFY_PASS';
	  $keys[4] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_HASH_ALGORITHM';

    ksort($keys);
    $keys = array_values($keys);

		return $keys;
	}

}
