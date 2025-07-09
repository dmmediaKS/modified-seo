<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_loworderfee.php 16160 2024-10-08 07:16:11Z GTB $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_loworderfee.php,v 1.11 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (ot_loworderfee.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   

  class ot_loworderfee {

    var $code;
    var $title;
    var $description;
    var $enabled;
    var $sort_order;
    var $output;
    var $_check;

    function __construct() {    	
      $this->code = 'ot_loworderfee';
      $this->title = MODULE_ORDER_TOTAL_LOWORDERFEE_TITLE;
      $this->description = MODULE_ORDER_TOTAL_LOWORDERFEE_DESCRIPTION;
      $this->enabled = ((defined('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS') && MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS == 'true') ? true : false);
      $this->sort_order = ((defined('MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER')) ? MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER : '');

      $this->output = array();
    }

    function process() {
      global $order, $xtPrice;
      
      switch (MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION) {
        case 'national':
          if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; 
          $low_order_fee_value_under = MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER;
          $low_order_fee_value = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
         break;
        case 'international':
          if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; 
          $low_order_fee_value_under = MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER_INTERNATIONAL;
          $low_order_fee_value = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE_INTERNATIONAL;
          break;
        case 'both':
          if ($order->delivery['country_id'] == STORE_COUNTRY) {
            $low_order_fee_value_under = MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER;
            $low_order_fee_value = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
          } else {
            $low_order_fee_value_under = MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER_INTERNATIONAL;
            $low_order_fee_value = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE_INTERNATIONAL;
          }
          $pass = true; 
          break;
        default:
          $pass = false; 
          break;
      }

      if ($pass == true 
          && $xtPrice->xtcRemoveCurr($order->info['total'] - $order->info['shipping_cost']) < $low_order_fee_value_under
          )
      {
        $lof_cost = $xtPrice->xtcCalculateCurr($low_order_fee_value);
        $lof_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
        $lof_tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

        $tax = $xtPrice->calcTax($lof_cost, $lof_tax);

        if ($tax > 0
            && defined('MODULE_ORDER_TOTAL_TAX_STATUS')
            && MODULE_ORDER_TOTAL_TAX_STATUS == 'true'
            )
        {
          if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
            if (!isset($order->info['tax_groups'][TAX_ADD_TAX . "$lof_tax_description"])) {
              $order->info['tax_groups'][TAX_ADD_TAX . "$lof_tax_description"] = 0;
            }
            $order->info['tax'] += $tax;
            $order->info['tax_groups'][TAX_ADD_TAX . "$lof_tax_description"] += $tax;
            $lof_cost = $xtPrice->xtcAddTax($lof_cost, $lof_tax, false);
          }
      
          if (($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
               && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
               ) || ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
                     && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
                     && $order->delivery['country_id'] == STORE_COUNTRY
                     )
              )
      
          {
            if (!isset($order->info['tax_groups'][TAX_NO_TAX . "$lof_tax_description"])) {
              $order->info['tax_groups'][TAX_NO_TAX . "$lof_tax_description"] = 0;
            }
            $order->info['tax'] += $tax;
            $order->info['tax_groups'][TAX_NO_TAX . "$lof_tax_description"] += $tax;
          }
        }

        $order->info['total'] += $lof_cost;
        
        $this->output[] = array(
          'title' => $this->title . ':',
          'text' => $xtPrice->xtcFormat($lof_cost, true),
          'value' => $xtPrice->xtcFormat($lof_cost, false),
        );
      }
    }

    function check() {
      if (!isset($this->_check)) {
        if (defined('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS')) {
          $this->_check = true;
        } else {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
      }
      return $this->_check;
    }

    function keys() {
      return array(
        'MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS', 
        'MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER', 
        'MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER',
        'MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER_INTERNATIONAL',
        'MODULE_ORDER_TOTAL_LOWORDERFEE_FEE', 
        'MODULE_ORDER_TOTAL_LOWORDERFEE_FEE_INTERNATIONAL', 
        'MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION', 
        'MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS'
      );
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER', '35', '6', '2', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER', '50','6', '4', 'currencies->format', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER_INTERNATIONAL', '50','6', '4', 'currencies->format', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_FEE', '5','6', '5', 'currencies->format', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_FEE_INTERNATIONAL', '5','6', '5', 'currencies->format', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION', 'both','6', '6', 'xtc_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS', '0','6', '7', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
