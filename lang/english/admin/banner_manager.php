<?php
/* --------------------------------------------------------------
   $Id: banner_manager.php 16409 2025-04-09 12:12:48Z GTB $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banner_manager.php,v 1.25 2003/02/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (banner_manager.php,v 1.4 2003/08/14); www.nextcommerce.org
   (c) 2006 xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

define('HEADING_TITLE', 'Banner Manager');

define('TABLE_HEADING_BANNERS', 'Banners');
define('TABLE_HEADING_GROUPS', 'Groups');
define('TABLE_HEADING_STATISTICS', 'Displays / Clicks');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_LANGUAGE', 'Language');
define('TABLE_HEADING_SORT', 'Sorting');
define('TABLE_HEADING_IMAGE', 'Image');

define('TEXT_BANNERS_TITLE', 'Banner Title:');
define('TEXT_BANNERS_URL', 'Banner URL:');
define('TEXT_BANNERS_URL_NOTE', 'URL target when clicking the banner.'); 
define('TEXT_BANNERS_REDIRECT', 'Direct Linking:'); 
define('TEXT_BANNERS_REDIRECT_NOTE', 'The banner will be linked directly to the given banner URL and not using "redirect.php?action=banner&amp;goto=xxx".<br /><strong>CAUTION:</strong> "Displays / Clicks" can no longer be counted if "Direct Linking" is enabled!');
define('TEXT_BANNERS_GROUP', 'Banner Group:');
define('TEXT_BANNERS_NEW_GROUP', 'Choose an existing banner group (if exists) or enter a new banner group below.');
define('TEXT_BANNERS_NEW_GROUP_NOTE', 'To display a Banner in the template, the template must be extended<br/>Example: Banner Group banner , the banner can be displayed in the template in the index.html with ${BANNER}');
define('TEXT_BANNERS_IMAGE', 'Image:');
define('TEXT_BANNERS_IMAGE_MOBILE', 'Image Mobile:'); 
define('TEXT_BANNERS_IMAGE_LOCAL', 'Choose the desired image by clicking the "Browse" button or choose an existing banner below.<br /><strong>Allowed extensions:</strong> jpg, jpeg, jpe, gif, png, bmp, tiff, tif, bmp, swf, cab');
define('TEXT_BANNERS_IMAGE_TARGET', 'Image Target (Save To):');
define('TEXT_BANNERS_HTML_TEXT', 'HTML Text:');
define('TEXT_BANNERS_HTML_TEXT_NOTE', 'Here you can enter directly a HTML code you received from an affiliate service to display the banner.');
define('TEXT_BANNERS_EXPIRES_ON', 'Expires On:');
define('TEXT_BANNERS_OR_AT', ', or at');
define('TEXT_BANNERS_IMPRESSIONS', 'impressions/views.');
define('TEXT_BANNERS_SCHEDULED_AT', 'Scheduled At:');
define('TEXT_BANNERS_BANNER_NOTE', '<b>Banner Notes:</b><ul><li>Use an image or HTML text for the banner - not both.</li><li>HTML Text has priority over an image</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<b>Image Notes:</b><ul><li>Uploading directories must have proper user (write) permissions setup!</li><li>Do not fill out the \'Save To\' field if you are not uploading an image to the webserver (ie, you are using a local (serverside) image).</li><li>The \'Save To\' field must be an existing directory with an ending slash (eg, banners/).</li></ul>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '<b>Expiry Notes:</b><ul><li>Only one of the two fields should be submitted</li><li>If the banner is not to expire automatically, then leave these fields blank</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<b>Schedule Notes:</b><ul><li>If a schedule is set, the banner will be activated on that date.</li><li>All scheduled banners are marked as deactive until their date has arrived, to which they will then be marked active.</li></ul>');
define('TEXT_BANNERS_IMAGE_TITLE', 'Image Title');
define('TEXT_BANNERS_IMAGE_TITLE_NOTE', '');
define('TEXT_BANNERS_IMAGE_ALT', 'Image description');
define('TEXT_BANNERS_IMAGE_ALT_NOTE', '');

define('TEXT_BANNERS_DATE_ADDED', 'Date Added:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', 'Scheduled At: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', 'Expires At: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', 'Expires At: <b>%s</b> impressions');
define('TEXT_BANNERS_STATUS_CHANGE', 'Status Change: %s');

define('TEXT_BANNERS_DATA', 'D<br />A<br />T<br />A');
define('TEXT_BANNERS_LAST_3_DAYS', 'Last 3 Days');
define('TEXT_BANNERS_BANNER_VIEWS', 'Banner Views');
define('TEXT_BANNERS_BANNER_CLICKS', 'Banner Clicks');
define('TEXT_BANNERS_SORT', 'Sorting:');
define('TEXT_BANNERS_SORT_NOTE', 'The order only affects dynamic sliders and not static banners.');

define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this banner?');
define('TEXT_INFO_DELETE_IMAGE', 'Delete banner image');

define('SUCCESS_BANNER_INSERTED', 'Success: The banner has been inserted.');
define('SUCCESS_BANNER_UPDATED', 'Success: The banner has been updated.');
define('SUCCESS_BANNER_REMOVED', 'Success: The banner has been removed.');
define('SUCCESS_BANNER_STATUS_UPDATED', 'Success: The status of the banner has been updated.');

define('ERROR_BANNER_TITLE_REQUIRED', 'Error: Banner title required.');
define('ERROR_BANNER_GROUP_REQUIRED', 'Error: Banner group required.');
define('ERROR_BANNER_IMAGE_HTML_REQUIRED', 'Error: Banner image or HTML text required.');
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Error: Target directory does not exist: %s');
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Error: Target directory is not writeable: %s');
define('ERROR_IMAGE_DOES_NOT_EXIST', 'Error: Image does not exist.');
define('ERROR_IMAGE_IS_NOT_WRITEABLE', 'Error: Image can not be removed.');
define('ERROR_UNKNOWN_STATUS_FLAG', 'Error: Unknown status flag.');

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', 'Error: Graphs directory does not exist. Please create a \'graphs\' directory inside \'images\'.');
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', 'Error: Graphs directory is not writeable.');

// BOF - Tomcraft - 2009-11-06 - Use variable TEXT_BANNERS_DATE_FORMAT
define('TEXT_BANNERS_DATE_FORMAT', 'YYYY-MM-DD');
// EOF - Tomcraft - 2009-11-06 - Use variable TEXT_BANNERS_DATE_FORMAT

define('TEXT_BANNERS_LANGUAGE', 'Language:');
define('TEXT_BANNERS_LANGUAGE_NOTE', 'Select the language where the banner should be displayed?');
define('TEXT_NO_FILE', '-- no file --');
define('TEXT_BANNERS_LATEST_STATISTICS', '%s days statistic');
define('TEXT_ACTIVATE_BANNERS_HISTORY', 'Banner Statistic activate:');
