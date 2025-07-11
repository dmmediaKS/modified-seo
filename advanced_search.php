<?php
/* -----------------------------------------------------------------------------------------
   $Id: advanced_search.php 16145 2024-10-01 17:47:01Z GTB $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(advanced_search.php,v 1.49 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (advanced_search.php,v 1.13 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_get_categories.inc.php');
require_once (DIR_FS_INC.'xtc_get_manufacturers.inc.php');

// create smarty elements
$smarty = new Smarty();

$popup_params = $main->getPopupParams();

$smarty->assign('popup_params', $popup_params);
$smarty->assign('FORM_ACTION', xtc_draw_form('advanced_search', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get').xtc_hide_session_id());
$smarty->assign('INPUT_KEYWORDS', xtc_draw_input_field('keywords', '', 'placeholder="'.IMAGE_BUTTON_SEARCH.'"'));
$smarty->assign('HELP_LINK', xtc_href_link(FILENAME_POPUP_SEARCH_HELP, $popup_params['link_parameters'], $request_type));
$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_search.gif', IMAGE_BUTTON_SEARCH));

$smarty->assign('SELECT_CATEGORIES',xtc_draw_pull_down_menu('categories_id', xtc_get_categories(array (array ('id' => '', 'text' => TEXT_ALL_CATEGORIES))), ((isset($_GET['categories_id'])) ? (int)$_GET['categories_id'] : '')));
$smarty->assign('ENTRY_SUBCAT',xtc_draw_checkbox_field('inc_subcat', '1', ((isset($_GET['inc_subcat'])) ? true : false)));
$smarty->assign('SELECT_MANUFACTURERS',xtc_draw_pull_down_menu('filter_id', xtc_get_manufacturers(array (array ('id' => '', 'text' => TEXT_ALL_MANUFACTURERS))), ((isset($_GET['filter_id'])) ? (int)$_GET['filter_id'] : '')));
$smarty->assign('SELECT_PFROM',xtc_draw_input_field('pfrom', ((isset($_GET['pfrom'])) ? (float)$_GET['pfrom'] : '')));
$smarty->assign('SELECT_PTO',xtc_draw_input_field('pto', ((isset($_GET['pto'])) ? (float)$_GET['pto'] : '')));
$smarty->assign('FORM_END', '</form>');

if (isset ($_GET['errorno']) && $_GET['errorno'] != '') {
  $_GET['errorno'] = (int)$_GET['errorno'];
  
	if (($_GET['errorno'] & 1) == 1) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_AT_LEAST_ONE_INPUT)));
	}
  if (($_GET['errorno'] & 2) == 2) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_KEYWORDS_MIN_LENGTH)));
	}
	if (($_GET['errorno'] & 10) == 10) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_INVALID_FROM_DATE)));
	}
	if (($_GET['errorno'] & 100) == 100) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_INVALID_TO_DATE)));
	}
	if (($_GET['errorno'] & 1000) == 1000) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_TO_DATE_LESS_THAN_FROM_DATE)));
	}
	if (($_GET['errorno'] & 10000) == 10000 || ($_GET['errorno'] & 110000) == 110000) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_PRICE_FROM_MUST_BE_NUM)));
	}
	if (($_GET['errorno'] & 100000) == 100000 || ($_GET['errorno'] & 110000) == 110000) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_PRICE_TO_MUST_BE_NUM)));
	}
	if (($_GET['errorno'] & 1000000) == 1000000) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_PRICE_TO_LESS_THAN_PRICE_FROM)));
	}
	if (($_GET['errorno'] & 10000000) == 10000000) {
	  $messageStack->add('advanced_search', stripslashes(str_replace('\n', '<br />', JS_INVALID_KEYWORDS)));
	}
}

if ($messageStack->size('advanced_search') > 0) {
  $smarty->assign('error_message', $messageStack->output('advanced_search'));
}

// build breadcrumb
$breadcrumb->add(NAVBAR_TITLE_ADVANCED_SEARCH, xtc_href_link(FILENAME_ADVANCED_SEARCH));

// include header
require(DIR_WS_INCLUDES.'header.php');

// include boxes
$display_mode = 'search';
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

$smarty->assign('language', $_SESSION['language']);

$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/advanced_search.html');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM'))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');