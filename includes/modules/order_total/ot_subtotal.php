<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_subtotal.php 15734 2024-02-21 10:29:47Z GTB $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_subtotal.php,v 1.7 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (ot_subtotal.php,v 1.10 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  class ot_subtotal {

    var $code;
    var $title;
    var $description;
    var $enabled;
    var $sort_order;
    var $output;
    var $_check;

    function __construct() {    	
      $this->code = 'ot_subtotal';
      $this->title = MODULE_ORDER_TOTAL_SUBTOTAL_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SUBTOTAL_DESCRIPTION;
      $this->enabled = ((defined('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS') && MODULE_ORDER_TOTAL_SUBTOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = ((defined('MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER')) ? MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER : '');
 
      $this->output = array();
    }

    function process() {
      global $order, $xtPrice;
      
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
          && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
          ) 
      {
        $this->output[] = array('title' => ((MODULE_SMALL_BUSINESS == 'true') ? $this->title : MODULE_ORDER_TOTAL_SUBTOTAL_TITLE_NO_TAX) . ':',
                                'text' => $xtPrice->xtcFormat($order->info['subtotal'], true),
                                'value' => $order->info['subtotal']);
      }
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 
          && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
          ) 
      {
        $this->output[] = array('title' => $this->title . ':',
                                'text' => $xtPrice->xtcFormat($order->info['subtotal'], true),
                                'value' => $order->info['subtotal']);
      } 
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
        $this->output[] = array('title' => $this->title . ':',
                                'text' => $xtPrice->xtcFormat($order->info['subtotal'], true),
                                'value' => $order->info['subtotal']);
      }


    }

    function check() {
      if (!isset($this->_check)) {
        if (defined('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS')) {
          $this->_check = true;
        } else {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
      }
      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER');
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER', '10', '6', '2', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
