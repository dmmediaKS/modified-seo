<?php
/* -----------------------------------------------------------------------------------------
   $Id: supermailer.php 15771 2024-03-01 11:45:29Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

// include needed functions
class supermailer {

  var $code;
  var $title;
  var $description;
  var $sort_order;
  var $enabled;
  var $_check;

  function __construct() {
     $this->code = 'supermailer';
     $this->title = MODULE_SUPERMAILER_TEXT_TITLE;
     $this->description = MODULE_SUPERMAILER_TEXT_DESCRIPTION;
     $this->sort_order = defined('MODULE_SUPERMAILER_SORT_ORDER') ? MODULE_SUPERMAILER_SORT_ORDER : '';
     $this->enabled = ((defined('MODULE_SUPERMAILER_STATUS') && MODULE_SUPERMAILER_STATUS == 'True') ? true : false);
  }

  function process($file) {

  }

  function display() {
    return array('text' => '<br /><div align="center">' . xtc_button(BUTTON_SAVE) .
                           xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=supermailer')) . "</div>");
  }

  function check() {
    if (!isset($this->_check)) {
      if (defined('MODULE_SUPERMAILER_STATUS')) {
        $this->_check = true;
      } else {
        $check_query = xtc_db_query("SELECT configuration_value 
                                       FROM " . TABLE_CONFIGURATION . " 
                                      WHERE configuration_key = 'MODULE_SUPERMAILER_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
    }
    return $this->_check;
  }
    
  function install() {
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SUPERMAILER_STATUS', 'False',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, date_added) VALUES ('MODULE_SUPERMAILER_EMAIL_ADDRESS', '',  '6', '1', '', now())");
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, date_added) VALUES ('MODULE_SUPERMAILER_GROUP', '',  '6', '1', '', now())");
  }

  function remove() {
    xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  function keys() {
    $key = array('MODULE_SUPERMAILER_STATUS',
                 'MODULE_SUPERMAILER_EMAIL_ADDRESS',
                 'MODULE_SUPERMAILER_GROUP');

    return $key;
  }
}
