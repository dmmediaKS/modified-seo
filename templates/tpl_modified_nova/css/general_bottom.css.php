<?php
/* -----------------------------------------------------------------------------------------
   $Id: general_bottom.css.php 15959 2024-06-20 15:37:40Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // This CSS file get includes at the BOTTOM of every template page in shop
  // you can add your template specific css scripts here
  defined('DIR_TMPL') OR define('DIR_TMPL', 'templates/'.CURRENT_TEMPLATE.'/');
  defined('DIR_TMPL_CSS') OR define('DIR_TMPL_CSS', DIR_TMPL.'css/');

  $css_array = array(
    DIR_TMPL_CSS.'jquery.colorbox.css',
    DIR_TMPL_CSS.'jquery.sumoselect.css',
    DIR_TMPL_CSS.'jquery.alertable.css',
    DIR_TMPL_CSS.'splide.css',
    DIR_TMPL_CSS.'jquery.viewer.css',
    DIR_TMPL_CSS.'jquery.mmenulight.css',
    DIR_TMPL_CSS.'fontawesome-6-custom.css',
    DIR_TMPL_CSS.'cookieconsent.css',
  );

  if (is_file(DIR_FS_CATALOG.DIR_TMPL_CSS.'tpl_custom_bottom.css')) {
    array_push($css_array, DIR_TMPL_CSS.'tpl_custom_bottom.css');
  }

  $css_min = DIR_TMPL_CSS.'tpl_plugins.min.css';

  $this_f_time = filemtime(DIR_FS_CATALOG.DIR_TMPL_CSS.'general_bottom.css.php');

  if (COMPRESS_STYLESHEET == 'true') {
    require_once(DIR_FS_BOXES_INC.'combine_files.inc.php');
    $css_array = combine_files($css_array,$css_min,true,$this_f_time);
  }
  
  foreach ($css_array as $css) {
    $css .= strpos($css,$css_min) === false ? '?v=' . filemtime(DIR_FS_CATALOG.$css) : '';
    echo '<link rel="stylesheet" property="stylesheet" href="'.DIR_WS_BASE.$css.'" type="text/css" media="screen" />'.PHP_EOL;
  }
