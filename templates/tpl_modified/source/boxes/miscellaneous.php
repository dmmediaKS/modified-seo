<?php
/* -----------------------------------------------------------------------------------------
   $Id: miscellaneous.php 15987 2024-06-28 09:51:10Z GTB $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(miscellaneous.php,v 1.6 2003/02/10); www.oscommerce.com 
   (c) 2003	nextcommerce (content.php,v 1.2 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include smarty
include(DIR_FS_BOXES_INC . 'smarty_default.php');

// set cache id
$cache_id = md5('lID:'.$_SESSION['language'].'|csID:'.$_SESSION['customers_status']['customers_status_id']);


if (!$cache) {
  $box_miscellaneous = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_miscellaneous.html');
} else {
  $box_miscellaneous = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_miscellaneous.html', $cache_id);
}

$smarty->assign('box_MISCELLANEOUS', $box_miscellaneous);
?>