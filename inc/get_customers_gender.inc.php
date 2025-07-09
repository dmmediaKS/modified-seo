<?php
/* -----------------------------------------------------------------------------------------
   $Id: get_customers_gender.inc.php 16320 2025-02-11 17:00:32Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


function get_customers_gender($id=false) 
{
  $gender_array = array(array('id' => '', 'text' => GENDER_NONE),
                        array('id' => 'm', 'text' => GENDER_MALE),
                        array('id' => 'f', 'text' => GENDER_FEMALE),
                        array('id' => 'd', 'text' => GENDER_DIVERSE),
                        );
  if ($id === false) {
    return $gender_array;
  } else {
    for ($i=0, $n=count($gender_array); $i<$n; $i++) {
      if ($gender_array[$i]['id'] == $id && $id != '') {
        return $gender_array[$i]['text'];
      }
    }
  }
  
  return '';
}
