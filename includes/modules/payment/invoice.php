<?php

/* -----------------------------------------------------------------------------------------
   $Id: invoice.php 16209 2024-11-21 08:46:57Z AGI $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003  nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class invoice {

  var $code;
  var $title;
  var $info;
  var $description;
  var $sort_order;
  var $enabled;
  var $order_status;
  var $min_order;
  var $_check;

  function __construct() {
    global $order;

    $this->code = 'invoice';
    $this->title = MODULE_PAYMENT_INVOICE_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION;
    $this->sort_order = ((defined('MODULE_PAYMENT_INVOICE_SORT_ORDER')) ? MODULE_PAYMENT_INVOICE_SORT_ORDER : '');
    $this->enabled = ((defined('MODULE_PAYMENT_INVOICE_STATUS') && MODULE_PAYMENT_INVOICE_STATUS == 'True') ? true : false);
    $this->info = MODULE_PAYMENT_INVOICE_TEXT_INFO;
    
    if ($this->check() > 0) {
      $this->min_order = MODULE_PAYMENT_INVOICE_MIN_ORDER;
      if ((int) MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID;
      }
    }
    
		if (!defined('RUN_MODE_ADMIN') && is_object($order)) {
			$this->update_status();
		}
  }

  function update_status() {
    global $order, $xtPrice;
    
    $customer_id = (isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : (isset($_SESSION['customer_gid']) ? (int)$_SESSION['customer_gid'] : 0));
    
    $check_order_query = xtc_db_query("SELECT COUNT(*) as count 
                                         FROM ".TABLE_ORDERS." 
                                        WHERE customers_id = '".$customer_id."' 
                                          AND orders_status IN (".MODULE_PAYMENT_INVOICE_MIN_ORDER_STATUS_ID.")");
    $order_check = xtc_db_fetch_array($check_order_query);

    if ($order_check['count'] < MODULE_PAYMENT_INVOICE_MIN_ORDER) {
      $check_flag = false;
      $this->enabled = false;
    } elseif (MODULE_PAYMENT_INVOICE_MAX_AMOUNT > 0 
              && $xtPrice->xtcRemoveCurr($order->info['total']) > MODULE_PAYMENT_INVOICE_MAX_AMOUNT
              )
    {
      $check_flag = false;
      $this->enabled = false;
    } else {
      $check_flag = true;

      if (($this->enabled == true) && ((int) MODULE_PAYMENT_INVOICE_ZONE > 0)) {
        $check_flag = false;
        $check_query = xtc_db_query("SELECT zone_id 
                                       FROM ".TABLE_ZONES_TO_GEO_ZONES." 
                                      WHERE geo_zone_id = '".(int)MODULE_PAYMENT_INVOICE_ZONE."' 
                                        AND zone_country_id = '".(int)$order->billing['country']['id']."' 
                                   ORDER BY zone_id");

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
    return false;
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
      if (defined('MODULE_PAYMENT_INVOICE_STATUS')) {
        $this->_check = true;
      } else {
        $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_INVOICE_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
    }
    return $this->_check;
  }

  function install() {
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_INVOICE_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_INVOICE_ALLOWED', '', '6', '0', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_INVOICE_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_INVOICE_SORT_ORDER', '0',  '6', '0', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_INVOICE_MIN_ORDER', '0',  '6', '0', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_INVOICE_MIN_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_display_orders_statuses', 'xtc_cfg_multi_checkbox(\'xtc_get_orders_status\', \'chr(44)\',', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_INVOICE_MAX_AMOUNT', '600',  '6', '0', now())");
  }

  function remove() {
    xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
  }

  function keys() {
    return array (
      'MODULE_PAYMENT_INVOICE_STATUS', 
      'MODULE_PAYMENT_INVOICE_ALLOWED', 
      'MODULE_PAYMENT_INVOICE_ZONE', 
      'MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', 
      'MODULE_PAYMENT_INVOICE_MIN_ORDER', 
      'MODULE_PAYMENT_INVOICE_MIN_ORDER_STATUS_ID', 
      'MODULE_PAYMENT_INVOICE_SORT_ORDER',
      'MODULE_PAYMENT_INVOICE_MAX_AMOUNT'
    );
  }
}
