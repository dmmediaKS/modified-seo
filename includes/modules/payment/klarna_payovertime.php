<?php
/* -----------------------------------------------------------------------------------------
   $Id: klarna_payovertime.php 15761 2024-02-29 18:59:48Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed classes
require_once(DIR_FS_EXTERNAL.'klarna/classes/KlarnaPayment.php');


class klarna_payovertime extends KlarnaPayment {

  var $code;
  var $klarna_code;

  function __construct() {
    global $order;

    $this->code = 'klarna_payovertime';
    $this->klarna_code = 'pay_over_time';

    KlarnaPayment::__construct($this->code);

    if (!defined('RUN_MODE_ADMIN') && is_object($order)) {
      $this->update_status();
    }
  }


  function selection() {
    $data = $this->get_method();
        
    $info = '<div id="klarna-payments-over-time"></div>
             <script>var klarna_'.$this->klarna_code.'_result = false;</script>';
    
    $_SESSION['klarna']['script'][$this->klarna_code] = '          
          Klarna.Payments.load({
            container: "#klarna-payments-over-time",
            payment_method_category: "'.$this->klarna_code.'"
          });';
    
    return array(
      'id' => $this->code, 
      'module' => ((MODULE_PAYMENT_KLARNA_TEXT != '') ? MODULE_PAYMENT_KLARNA_TEXT.' ' : '').decode_utf8($data['name']), 
      'description' => $info,
    );
  }

}