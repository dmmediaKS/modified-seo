<?php
  /* --------------------------------------------------------------
   $Id: trustedshops.php 15968 2024-06-27 10:47:15Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   Released under the GNU General Public License
   --------------------------------------------------------------*/
  
  // language
  define('TEXT_GUEST_1', 'Guest');  
  define('TEXT_GUEST_2', 'Gast');
  
  $script_begin = '';
  $script_src = '';
  if (defined('MODULE_COOKIE_CONSENT_STATUS') 
      && MODULE_COOKIE_CONSENT_STATUS == 'true' 
      && (in_array(10, $_SESSION['tracking']['allowed']) 
          || defined('COOKIE_CONSENT_NO_TRACKING')
          )
      )
  {
    $script_begin = 'data-type="text/javascript" type="as-oil" data-purposes="10" data-managed="as-oil"';
    $script_src = 'data-';
  }

  // defaults
  $custom_trustbadge_code = '
<script async '.$script_begin.'
  data-desktop-y-offset="0" 
  data-mobile-y-offset="0"
  data-desktop-disable-reviews="false" 
  data-desktop-enable-custom="false" 
  data-desktop-position="right" 
  data-desktop-custom-width="156" 
  data-desktop-enable-fadeout="false"
  data-disable-mobile="false" 
  data-disable-trustbadge="false" 
  data-mobile-custom-width="156" 
  data-mobile-disable-reviews="false" 
  data-mobile-enable-custom="false" 
  data-mobile-position="right" 
  charset="utf-8" 
  '.$script_src.'src="//widgets.trustedshops.com/js/%s.js"> 
</script>';

  $default_trustbadge_code = '
<script async '.$script_begin.'
  data-desktop-y-offset="%s" 
  data-mobile-y-offset="%s"
  data-desktop-disable-reviews="%s"   
  data-mobile-disable-reviews="%s" 
  data-desktop-position="%s" 
  data-mobile-position="%s" 
  charset="utf-8" 
  '.$script_src.'src="//widgets.trustedshops.com/js/%s.js"> 
</script>';
