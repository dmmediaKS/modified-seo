<?php
/* -----------------------------------------------------------------------------------------
   $Id: freeamount.php 16389 2025-04-01 16:32:15Z GTB $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(freeamount.php,v 1.01 2002/01/24); www.oscommerce.com 
   (c) 2003  nextcommerce (freeamount.php,v 1.12 2003/08/24); www.nextcommerce.org
   (c) 2006 xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  class freeamount {

    var $code;
    var $title;
    var $description;
    var $sort_order;
    var $icon;
    var $tax_class;
    var $enabled;
    var $num_zones;
    var $quotes;
    var $_check;

    function __construct() {
      global $order;

      $this->code = 'freeamount';
      $this->title = MODULE_SHIPPING_FREEAMOUNT_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FREEAMOUNT_TEXT_DESCRIPTION;
      $this->icon ='';   // change $this->icon =  DIR_WS_ICONS . 'shipping_ups.gif'; to some freeshipping icon
      $this->tax_class = ((defined('MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS')) ? MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS : '');
      $this->sort_order = ((defined('MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER')) ? MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER : '');
      $this->enabled = ((defined('MODULE_SHIPPING_FREEAMOUNT_STATUS') && MODULE_SHIPPING_FREEAMOUNT_STATUS == 'True') ? true : false);
      $this->num_zones = defined('MODULE_SHIPPING_FREEAMOUNT_NUMBER_ZONES') ? (int)MODULE_SHIPPING_FREEAMOUNT_NUMBER_ZONES : 0;

      if ($this->enabled == true 
          && !defined('RUN_MODE_ADMIN')
          && (int)MODULE_SHIPPING_FREEAMOUNT_ZONE > 0 
          && is_object($order)
          )
      {
        $check_flag = false;
        $check_query = xtc_db_query("SELECT zone_id 
                                       FROM " . TABLE_ZONES_TO_GEO_ZONES . "
                                      WHERE geo_zone_id = '" . (int)MODULE_SHIPPING_FREEAMOUNT_ZONE . "' 
                                        AND zone_country_id = '" . (int)$order->delivery['shipping']['id'] . "' 
                                   ORDER BY zone_id");
        while ($check = xtc_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['shipping']['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
      
      if ($this->check() > 0) {
        //update compatibility
        if (!defined('MODULE_SHIPPING_FREEAMOUNT_NUMBER_ZONES')) {
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_NUMBER_ZONES', '1', '6', '0', now())");
          if (defined('MODULE_SHIPPING_FREEAMOUNT_AMOUNT')) {
            if (!defined('MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_1')) {
              xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_1', '". MODULE_SHIPPING_FREEAMOUNT_ALLOWED ."', '6', '0', 'xtc_cfg_textarea(', now())");
            }
            if (!defined('MODULE_SHIPPING_FREEAMOUNT_AMOUNT_1')) {
              xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_AMOUNT_1', '". MODULE_SHIPPING_FREEAMOUNT_AMOUNT ."', '6', '0', now())");
            }
          }
        }
        if (!defined('MODULE_SHIPPING_FREEAMOUNT_ZONE')) {
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        }    
        if (!defined('MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS')) {
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
        }
        
        $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_%'");
        $check_zones_rows = xtc_db_num_rows($check_zones_query);

        if ($check_zones_rows != $this->num_zones) {
          $this->install_zones($check_zones_rows);
        }
      }
    }

    function quote($method = '') {
      global $xtPrice, $order;
  
      $dest_country = $order->delivery['shipping']['iso_code_2'];
      $dest_zone = 0;
      
      for ($i=1; $i<=$this->num_zones; $i++) {
        $countries_table = constant('MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_' . $i);
        $countries_table  = preg_replace("'[\r\n\s]+'",'',$countries_table);
        $country_zones = explode(',', $countries_table);
        if (in_array($dest_country, $country_zones)) {
          $dest_zone = $i;
          break;
        }
        // rest of the world
        if ($countries_table == 'WORLD') {
          $dest_zone = $i;
        }
        // rest of the world eof
      }

      $this->quotes = array('id' => $this->code,
                            'module' => $this->title);

      if ($dest_zone == 0) {
        if (MODULE_SHIPPING_FREEAMOUNT_DISPLAY == 'True') {
          $this->quotes['error'] = MODULE_SHIPPING_FREEAMOUNT_INVALID_ZONE;
        } else {
          $this->enabled = false;
        }
      } else {
        $freeamount_zone = (double)constant('MODULE_SHIPPING_FREEAMOUNT_AMOUNT_' . $dest_zone);

        if (( $xtPrice->xtcRemoveCurr($order->info['subtotal']) < $freeamount_zone) && MODULE_SHIPPING_FREEAMOUNT_DISPLAY == 'False') {
          $this->enabled = false;
        }

        if ($xtPrice->xtcRemoveCurr($order->info['subtotal']) < $freeamount_zone) {
          $this->quotes['error'] = sprintf(MODULE_SHIPPING_FREEAMOUNT_TEXT_WAY, $xtPrice->xtcFormat($freeamount_zone, true, 0, true));
        } else {
          $this->quotes['methods'] = array(array('id' => $this->code,
                                                 'title' => sprintf(MODULE_SHIPPING_FREEAMOUNT_TEXT_WAY, $xtPrice->xtcFormat($freeamount_zone, true, 0, true)),
                                                 'cost'  => 0));
        }
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);
      
      if ($this->enabled)
        return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        if (defined('MODULE_SHIPPING_FREEAMOUNT_STATUS')) {
          $this->_check = true;
        } else {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_STATUS', 'True', '6', '7', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_ALLOWED', '', '6', '0', 'xtc_cfg_textarea(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_DISPLAY', 'True', '6', '7', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER', '0', '6', '4', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_NUMBER_ZONES', '1', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");

      $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_".strtoupper($this->code)."_COUNTRIES_%'");
      $check_zones_rows_query = xtc_db_num_rows($check_zones_query);

      if ($check_zones_rows_query != 0) {
        $this->install_zones($check_zones_rows_query);
        xtc_db_query("UPDATE ".TABLE_CONFIGURATION." 
                         SET configuration_value = '".(int)$check_zones_rows_query."' 
                       WHERE configuration_key = 'MODULE_SHIPPING_".strtoupper($this->code)."_NUMBER_ZONES'");
      }
    }

    function install_zones($number_of_zones) {
                    
      // backup old values
      xtc_backup_configuration($this->keys_zones($number_of_zones));

      // add new zone
      if ($number_of_zones <= $this->num_zones) {
        for ($i = (($number_of_zones == 0) ? 1 : $number_of_zones); $i <= $this->num_zones; $i ++) {
          $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_".$i."'");
          if (xtc_db_num_rows($check_zones_query) < 1) {
            xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_".$i."', '', '6', '0', 'xtc_cfg_textarea(', now())");
            xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_AMOUNT_".$i."', '', '6', '0', now())");
          }
        }      
      } else {
        // remove zone
        for ($i = $number_of_zones; $i >= $this->num_zones; $i --) {
          xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_".$i."'");
          xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_".$i."'");      
        }
      }

      // set standard values
      for ($i = 1; $i <= $this->num_zones; $i ++) {
        if ($i == 1) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'DE' WHERE configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_1'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '50.00' WHERE  configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_1'");
        }
        if ($i == 2) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AT,BE,BG,CY,CZ,DK,EE,ES,FI,FR,GB,GR,HU,IE,IT,LT,LU,LV,MC,MT,NL,PL,PT,RO,SE,SI,SK' WHERE configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_2'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '100.00' WHERE  configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_2'");
        }
        if ($i == 3) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AD,AL,AM,AZ,BA,BY,CH,FO,GE,GI,GL,HR,IS,KZ,LI,MD,ME,MK,NO,RS,RU,SM,TR,UA,VA' WHERE configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_3'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '150.00' WHERE  configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_3'");
        }
        if ($i == 4) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'CA,DZ,EG,IL,JO,LB,LR,LY,MA,PM,PS,SY,TN,US' WHERE configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_4'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '200.00' WHERE  configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_4'");
        }
        if ($i == 5) {
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AE,AF,AG,AI,AN,AO,AR,AU,AW,BB,BD,BF,BH,BI,BJ,BM,BN,BO,BR,BS,BT,BW,BZ,CD,CF,CG,CI,CK,CL,CM,CN,CO,CR,CU,CV,DJ,DM,DO,EC,ER,ET,FJ,FK,FM,GA,GD,GF,GH,GM,GN,GP,GQ,GT,GU,GW,GY,HK,HN,HT,ID,IN,IQ,IR,JM,JP,KE,KG,KH,KI,KM,KN,KP,KR,KW,KY,LA,LC,LK,LS,MG,MH,ML,MM,MN,MO,MP,MQ,MR,MS,MU,MV,MW,MX,MY,MZ,NA,NC,NE,NG,NI,NP,NR,NZ,OM,PA,PE,PF,PG,PH,PK,PN,PR,PY,QA,RE,RW,SA,SB,SC,SD,SG,SH,SL,SN,SO,SR,ST,SV,SZ,TC,TD,TG,TH,TJ,TM,TO,TT,TV,TW,TZ,UG,UY,UZ,VC,VE,VN,VU,WF,WS,YE,ZA,ZM,ZW' WHERE configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_5'");
          xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '250.00' WHERE  configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_5'");
        }
      }
      
      // restore old values
      xtc_restore_configuration($this->keys_zones($this->num_zones));
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE_SHIPPING_FREEAMOUNT_%'");
    }

    function keys_zones($zones) {
      $keys_zones = array();
      for ($i = 1; $i <= $zones; $i ++) {
        $keys_zones[] = 'MODULE_SHIPPING_FREEAMOUNT_COUNTRIES_' . $i;
        $keys_zones[] = 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT_' . $i;
      }
      return $keys_zones;
    }

    function keys() {
      $keys = array(
        'MODULE_SHIPPING_FREEAMOUNT_STATUS',
        'MODULE_SHIPPING_FREEAMOUNT_ALLOWED',
        'MODULE_SHIPPING_FREEAMOUNT_ZONE',
        'MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER',
        'MODULE_SHIPPING_FREEAMOUNT_NUMBER_ZONES',
        'MODULE_SHIPPING_FREEAMOUNT_DISPLAY',
      );
      $keys = array_merge($keys, $this->keys_zones($this->num_zones));

      return $keys;
    }
  }
