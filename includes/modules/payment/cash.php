<?php

/* -----------------------------------------------------------------------------------------
   $Id: cash.php 16119 2024-09-09 09:52:15Z GTB $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneyorder.php,v 1.10 2003/01/29); www.oscommerce.com
   (c) 2003  nextcommerce (moneyorder.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class cash {

  var $code;
  var $title;
  var $info;
  var $description;
  var $sort_order;
  var $enabled;
  var $order_status;
  var $email_footer;
  var $_check;

  function __construct() {
    global $order;

    $this->code = 'cash';
    $this->title = MODULE_PAYMENT_CASH_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_CASH_TEXT_DESCRIPTION;
    $this->sort_order = ((defined('MODULE_PAYMENT_CASH_SORT_ORDER')) ? MODULE_PAYMENT_CASH_SORT_ORDER : '');
    $this->enabled = ((defined('MODULE_PAYMENT_CASH_STATUS') && MODULE_PAYMENT_CASH_STATUS == 'True') ? true : false);
    $this->info = MODULE_PAYMENT_CASH_TEXT_INFO;
    if ($this->check() > 0) {
      if ((int) MODULE_PAYMENT_CASH_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_CASH_ORDER_STATUS_ID;
      }
    }
		if (!defined('RUN_MODE_ADMIN') && is_object($order)) {
			$this->update_status();
		}
    $this->email_footer = MODULE_PAYMENT_CASH_TEXT_EMAIL_FOOTER;
  }

  function update_status() {
    global $order;

    if (!isset($_SESSION['shipping'])
        || !is_array($_SESSION['shipping'])
        || (array_key_exists('id', $_SESSION['shipping']) 
            && strpos($_SESSION['shipping']['id'], 'selfpickup') === false
            )
        )
    {
      $this->enabled = false;
    }

    if (($this->enabled == true) && ((int) MODULE_PAYMENT_CASH_ZONE > 0)) {
      $check_flag = false;
      $check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_CASH_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
      while ($check = xtc_db_fetch_array($check_query)) {
        if ($check['zone_id'] < 1) {
          $check_flag = true;
          break;
        }
        elseif ($check['zone_id'] == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
  }

  function javascript_validation() {
    return false;
  }

  function selection() {
    return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
  }

  function pre_confirmation_check() {
    return false;
  }

  function confirmation() {
    return array ('title' => MODULE_PAYMENT_CASH_TEXT_DESCRIPTION);
  }

  function process_button() {
    return false;
  }

  function before_process() {
    return false;
  }

  function after_process() {
    global $insert_id;
    
    if (isset($this->order_status) && $this->order_status) {
      $orders_query = xtc_db_query("SELECT *
                                      FROM ".TABLE_ORDERS."
                                     WHERE orders_id = '".$insert_id."'");
      $orders = xtc_db_fetch_array($orders_query);
      
      if ($this->order_status != $orders['orders_status']) {
        xtc_db_query("UPDATE ".TABLE_ORDERS." 
                         SET orders_status = '".$this->order_status."' 
                       WHERE orders_id = '".(int)$insert_id."'");

        $sql_data_array = array(
          'orders_id' => (int)$insert_id,
          'orders_status_id' => $this->order_status,
          'date_added' => 'now()',
        );
        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
      }
    }
  }

  function get_error() {
    return false;
  }

  function check() {
    if (!isset ($this->_check)) {
      if (defined('MODULE_PAYMENT_CASH_STATUS')) {
        $this->_check = true;
      } else {
        $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_CASH_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
    }
    return $this->_check;
  }

  function install() {
    xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_CASH_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_CASH_ALLOWED', '',   '6', '0', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_CASH_SORT_ORDER', '0', '6', '0', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_CASH_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_CASH_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
  
    // install selfpickup
    if (is_file(DIR_FS_CATALOG_MODULES . 'shipping/selfpickup.php')) {
      require_once(DIR_FS_CATALOG_MODULES . 'shipping/selfpickup.php');
      include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/shipping/selfpickup.php');
      
      $selfpickup = new selfpickup();
      if ($selfpickup->check() < 1) {
        $selfpickup->install();

        require_once(DIR_FS_INC.'update_module_configuration.inc.php');
        update_module_configuration('shipping');
      }
    }
  }

  function remove() {
    xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
  }

  function keys() {
    return array (
      'MODULE_PAYMENT_CASH_STATUS', 
      'MODULE_PAYMENT_CASH_ALLOWED', 
      'MODULE_PAYMENT_CASH_ZONE', 
      'MODULE_PAYMENT_CASH_ORDER_STATUS_ID', 
      'MODULE_PAYMENT_CASH_SORT_ORDER'
    );
  }
}
