<?php

/* -----------------------------------------------------------------------------------------
   $Id: cod.php 16119 2024-09-09 09:52:15Z GTB $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com
   (c) 2003  nextcommerce (cod.php,v 1.7 2003/08/24); www.nextcommerce.org

   third party contributions:
   - added max subtotal where cod allowed to config, noRiddle / web0null / web28
   - added not showing cod on checkout_payment when shipping module doesn't offer cod
     or when fee in ot_cod_fee empty, noRiddle / web0null

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class cod {

  var $code;
  var $title;
  var $info;
  var $description;
  var $sort_order;
  var $enabled;
  var $order_status;
  var $_check;
  
  var $cost;
  var $limit_subtotal;

  function __construct() {
    global $order,$xtPrice;

    $this->code = 'cod';
    $this->title = MODULE_PAYMENT_COD_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_COD_TEXT_DESCRIPTION;
    $this->sort_order = ((defined('MODULE_PAYMENT_COD_SORT_ORDER')) ? MODULE_PAYMENT_COD_SORT_ORDER : '');
    $this->enabled = ((defined('MODULE_PAYMENT_COD_STATUS') && MODULE_PAYMENT_COD_STATUS == 'True') ? true : false);
    $this->info = ((defined('MODULE_PAYMENT_COD_DISPLAY_INFO') && MODULE_PAYMENT_COD_DISPLAY_INFO == 'True') ? MODULE_PAYMENT_COD_TEXT_DESCRIPTION.'<br />'.MODULE_PAYMENT_COD_TEXT_INFO : MODULE_PAYMENT_COD_TEXT_DESCRIPTION);
    $this->cost = '';    
    
    if ($this->check() > 0) {
      $this->limit_subtotal = (double)MODULE_PAYMENT_COD_LIMIT_ALLOWED;
      if ((int) MODULE_PAYMENT_COD_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_COD_ORDER_STATUS_ID;
      }
    }
    
		if (!defined('RUN_MODE_ADMIN') && is_object($order)) {
			$this->update_status();
		}
  }

  function update_status() {
    global $order;
    
    if (isset($_SESSION['shipping']) 
        && is_array($_SESSION['shipping'])
        && array_key_exists('id', $_SESSION['shipping']) 
        && strpos($_SESSION['shipping']['id'], 'selfpickup') !== false
        )
    {
      $this->enabled = false;
    }
    
    if (($this->enabled == true) && ((int) MODULE_PAYMENT_COD_ZONE > 0)) {
      $check_flag = false;
      $check_query = xtc_db_query("SELECT zone_id 
                                     FROM ".TABLE_ZONES_TO_GEO_ZONES." 
                                    WHERE geo_zone_id = '".(int)MODULE_PAYMENT_COD_ZONE."' 
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

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }

  }

  function javascript_validation() {
    return false;
  }

  function selection() {
    global $xtPrice, $order;

    // limit sum where cod allowed
    if ($this->limit_subtotal && ($xtPrice->xtcRemoveCurr($_SESSION['cart']->show_total()) >= $this->limit_subtotal)) {
      return;
    }
    
    if (defined('MODULE_ORDER_TOTAL_COD_FEE_STATUS')
        && MODULE_ORDER_TOTAL_COD_FEE_STATUS == 'true'
        )
    {
      //process installed shipping modules
      $shipping_code = '';
      if (isset($_SESSION['shipping']) 
          && is_array($_SESSION['shipping'])
          && array_key_exists('id', $_SESSION['shipping'])
          )
      {
        $shipping_array = explode('_', $_SESSION['shipping']['id']);
        $shipping_code = strtoupper(array_shift($shipping_array));
      }
      $shipping_code = (isset($shipping_code) && $shipping_code == 'FREEAMOUNT') ? 'FREEAMOUNT_FREE' : 'FEE_' . $shipping_code;

      $cod_zones = array();
      if (defined('MODULE_ORDER_TOTAL_COD_'. $shipping_code)) {
        $cod_zones = preg_split("/[:,]/", constant('MODULE_ORDER_TOTAL_COD_'. $shipping_code));
      }
      
      $cod_cost = 0;
      $cod_country = false;
      for ($i = 0; $i < count($cod_zones); $i++) {
        if ($cod_zones[$i] == $order->delivery['country']['iso_code_2']) {
          $cod_cost = $cod_zones[$i + 1];
          $cod_country = true;
          break;
        } elseif ($cod_zones[$i] == '00') {
          $cod_cost = $cod_zones[$i + 1];
          $cod_country = true;
          break;
        }
        $i++;
      }

      if ($cod_country && $cod_cost != '') {
        $cod_cost = $xtPrice->xtcCalculateCurr($cod_cost);
        $cod_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
        $cod_tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

        $tax = $xtPrice->calcTax($cod_cost, $cod_tax);
        
        if ($tax > 0
            && defined('MODULE_ORDER_TOTAL_TAX_STATUS')
            && MODULE_ORDER_TOTAL_TAX_STATUS == 'true'
            )
        {
          if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
            $cod_cost = $xtPrice->xtcAddTax($cod_cost, $cod_tax, false);
          }
        }
        
        $this->cost = '+ '.$xtPrice->xtcFormat($cod_cost, true);
      } else {
        return false;
      }
    }
    
    return array ('id' => $this->code,
                  'module' => $this->title,
                  'description' => $this->info,
                  'module_cost' => $this->cost
                 );
  }

  function pre_confirmation_check() {
    return false;
  }

  function confirmation() {
    return false;
  }

  function process_button() {
    $note = '';
    if (defined('MODULE_PAYMENT_COD_DISPLAY_INFO') && MODULE_PAYMENT_COD_DISPLAY_INFO == 'True') {
      $note = MODULE_PAYMENT_COD_DISPLAY_INFO_TEXT;
    }
    return $note;
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
      if (defined('MODULE_PAYMENT_COD_STATUS')) {
        $this->_check = true;
      } else {
        $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_COD_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
    }
    return $this->_check;
  }

  function install() {
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_COD_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_COD_ALLOWED', '', '6', '0', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_COD_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_COD_SORT_ORDER', '0',  '6', '0', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_COD_ORDER_STATUS_ID', '0','6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_COD_LIMIT_ALLOWED', '600', '6', '3', now())");
    xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_COD_DISPLAY_INFO', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
  }

  function remove() {
    xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
  }

  function keys() {
    return array (
      'MODULE_PAYMENT_COD_STATUS',
      'MODULE_PAYMENT_COD_ALLOWED',
      'MODULE_PAYMENT_COD_ZONE',
      'MODULE_PAYMENT_COD_ORDER_STATUS_ID',
      'MODULE_PAYMENT_COD_SORT_ORDER',
      'MODULE_PAYMENT_COD_LIMIT_ALLOWED',
      'MODULE_PAYMENT_COD_DISPLAY_INFO',
    );
  }
}
