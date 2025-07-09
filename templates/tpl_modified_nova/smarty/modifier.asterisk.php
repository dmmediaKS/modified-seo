<?php
/* -----------------------------------------------------------------------------------------
   $Id: modifier.asterisk.php 15291 2023-07-06 11:46:25Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  function smarty_modifier_asterisk($string) {  
    $string = str_replace("*", TEXT_ICON_ASTERISK, $string);

    return $string;
  }
