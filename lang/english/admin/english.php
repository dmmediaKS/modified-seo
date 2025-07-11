<?php
/* --------------------------------------------------------------
   $Id: english.php 16320 2025-02-11 17:00:32Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.99 2003/05/28); www.oscommerce.com
   (c) 2003 nextcommerce (german.php,v 1.24 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce (english.php)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_GB'
// on FreeBSD 4.0 I use 'en_GB.ISO_8859-1'
// this may not work under win32 environments..

@setlocale(LC_TIME, 'en_GB.UTF-8', 'en_GB@euro', 'en_GB', 'en-GB', 'en', 'en_GB.ISO_8859-1', 'English','en_GB.ISO_8859-15');

define('DATE_LOCALE', 'en_GB');
define('DATE_FORMAT_SHORT', 'd/m/Y');
define('DATE_FORMAT_LONG', 'l d F, Y');
define('DATE_FORMAT', DATE_FORMAT_SHORT);
define('PHP_DATE_TIME_FORMAT',  DATE_FORMAT_SHORT . ' H:i:s');
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' H:i:s');

// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function xtc_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

require_once(DIR_FS_INC.'auto_include.inc.php');
foreach(auto_include(DIR_FS_LANGUAGES.'english/extra/admin/','php') as $file) require ($file);

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="en"');

// page title
define('TITLE', defined('PROJECT_VERSION') ? PROJECT_VERSION : 'undefined');

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'Administration');
define('HEADER_TITLE_SUPPORT_SITE', 'Support Site');
define('HEADER_TITLE_ONLINE_CATALOG', 'Online Catalog');
define('HEADER_TITLE_ADMINISTRATION', 'Administration');

// text for gender
define('GENDER_NONE', 'none');
define('GENDER_MALE', 'Mr.');
define('GENDER_FEMALE', 'Ms./Mrs.');
define('GENDER_DIVERSE', 'Diverse');

// text for date of birth example
define('DOB_FORMAT_STRING', 'dd/mm/yyyy');

// configuration box text in includes/boxes/configuration.php

define('BOX_HEADING_CONFIGURATION','Configuration');
define('BOX_HEADING_MODULES','Modules');
define('BOX_HEADING_PARTNER_MODULES','Partner Modules');
define('BOX_HEADING_ZONE','Zones/Tax');
define('BOX_HEADING_CUSTOMERS','Customers');
define('BOX_HEADING_PRODUCTS','Catalog');
define('BOX_HEADING_STATISTICS','Statistics');
define('BOX_HEADING_TOOLS','Tools');
define('BOX_HEADING_LOCALIZATION', 'Languages/Currencies');
define('BOX_HEADING_TEMPLATES','Templates');
define('BOX_HEADING_LOCATION_AND_TAXES', 'Location/Tax');
define('BOX_HEADING_CATALOG', 'Catalog');

define('BOX_CONTENT','Content Manager');
define('TEXT_ALLOWED', 'Permission');
define('TEXT_ACCESS', 'Usable Area');
define('BOX_CONFIGURATION', 'General Options');
define('BOX_CONFIGURATION_1', 'My Shop');
define('BOX_CONFIGURATION_2', 'Minimum Values');
define('BOX_CONFIGURATION_3', 'Maximum Values');
define('BOX_CONFIGURATION_4', 'Image Options');
define('BOX_CONFIGURATION_5', 'Customer Details');
define('BOX_CONFIGURATION_6', 'Module Options');
define('BOX_CONFIGURATION_7', 'Shipping Options');
define('BOX_CONFIGURATION_8', 'Product Listing Options');
define('BOX_CONFIGURATION_9', 'Stock Options');
define('BOX_CONFIGURATION_10', 'Logging Options');
define('BOX_CONFIGURATION_11', 'Cache Options');
define('BOX_CONFIGURATION_12', 'E-Mail Options');
define('BOX_CONFIGURATION_13', 'Download Options');
define('BOX_CONFIGURATION_14', 'Compression');
define('BOX_CONFIGURATION_15', 'Sessions');
define('BOX_CONFIGURATION_16', 'Meta-Tags/Searchengines');
define('BOX_CONFIGURATION_17', 'Additional Modules');
define('BOX_CONFIGURATION_18', 'VAT Reg No');
define('BOX_CONFIGURATION_19', 'Partner');
define('BOX_CONFIGURATION_22', 'Search-Options');
define('BOX_CONFIGURATION_24', 'Google, Matomo &amp; Facebook');
define('BOX_CONFIGURATION_25', 'Captcha');
define('BOX_CONFIGURATION_31', 'Skrill');
define('BOX_CONFIGURATION_40', 'Popup Window Options');
define('BOX_CONFIGURATION_1000', 'My Admin');

define('BOX_MODULES', 'Payment-/Shipping-/Billing-Modules');
define('BOX_PAYMENT', 'Payment Methods');
define('BOX_SHIPPING', 'Shipping Methods');
define('BOX_ORDER_TOTAL', 'Order Total');
define('BOX_CATEGORIES', 'Categories/Products');
define('BOX_PRODUCTS_ATTRIBUTES', 'Product Options');
define('BOX_MANUFACTURERS', 'Manufacturers');
define('BOX_REVIEWS', 'Product Reviews');
define('BOX_CAMPAIGNS', 'Campaigns');
define('BOX_XSELL_PRODUCTS', 'Cross Marketing');
define('BOX_SPECIALS', 'Special Pricing');
define('BOX_PRODUCTS_EXPECTED', 'Expected Offers');
define('BOX_CUSTOMERS', 'Customers');
define('BOX_ACCOUNTING', 'Admin Permissions');
define('BOX_CUSTOMERS_STATUS','Customer Groups');
define('BOX_ORDERS', 'Orders');
define('BOX_COUNTRIES', 'Countries');
define('BOX_ZONES', 'Zones');
define('BOX_GEO_ZONES', 'Tax Zones');
define('BOX_TAX_CLASSES', 'Tax Classes');
define('BOX_TAX_RATES', 'Tax Rates');
define('BOX_HEADING_REPORTS', 'Reports');
define('BOX_PRODUCTS_VIEWED', 'Viewed Products');
define('BOX_STOCK_WARNING','Stock Info');
define('BOX_PRODUCTS_PURCHASED', 'Sold Products');
define('BOX_STATS_CUSTOMERS', 'Sales Statistics');
define('BOX_BACKUP', 'Database Manager');
define('BOX_BANNER_MANAGER', 'Banner Manager');
define('BOX_CACHE', 'Cache Control');
define('BOX_DEFINE_LANGUAGE', 'Language Definitions');
define('BOX_FILE_MANAGER', 'File-Manager');
define('BOX_MAIL', 'E-Mail Center');
define('BOX_NEWSLETTERS', 'Notification Manager');
define('BOX_SERVER_INFO', 'Server Info');
define('BOX_WHOS_ONLINE', 'Who is Online');
define('BOX_TPL_BOXES','Boxes Sort Order');
define('BOX_CURRENCIES', 'Currencies');
define('BOX_LANGUAGES', 'Languages');
define('BOX_ORDERS_STATUS', 'Order Status');
define('BOX_ATTRIBUTES_MANAGER','Attribute Manager');
define('BOX_MODULE_NEWSLETTER','Newsletter');
define('BOX_SHIPPING_STATUS','Shipping status');
define('BOX_SALES_REPORT','Sales Report');
define('BOX_MODULE_EXPORT','Export Modules');
define('BOX_MODULE_SYSTEM','System Modules');
define('BOX_HEADING_GV_ADMIN', 'Vouchers/Coupons');
define('BOX_GV_ADMIN_QUEUE', 'Gift Voucher Queue');
define('BOX_GV_ADMIN_MAIL', 'Mail Gift Voucher');
define('BOX_GV_ADMIN_SENT', 'Gift Vouchers sent');
define('BOX_COUPON_ADMIN','Coupon Admin');
define('BOX_TOOLS_BLACKLIST','Credit Card-Blacklist');
define('BOX_IMPORT','Import/Export'); 
define('BOX_PRODUCTS_VPE','Packing unit');
define('BOX_CAMPAIGNS_REPORT','Campaign report');
define('BOX_ORDERS_XSELL_GROUP','Cross-sell groups');
define('BOX_REMOVEOLDPICS','Remove old pictures'); // Remove old pictures - franky_n - 20110105
define('BOX_JANOLAW','janolaw AGB Hosting'); // Tomcraft - 2011-06-17 - Added janolaw AGB hosting service
define('BOX_HAENDLERBUND','H&auml;ndlerbund AGB Service'); // Tomcraft - 2012-12-08 - Added haendlerbund AGB interface
define('BOX_SHOP','Shop');
define('BOX_LOGOUT','Logout');
define('BOX_CREDITS','Credits');
define('BOX_UPDATE','Check Version');
define('BOX_GV_CUSTOMERS','Customers Credit');
define('BOX_IT_RECHT_KANZLEI', 'IT Recht Kanzlei');
define('BOX_PROTECTEDSHOPS', 'Protected Shops - AGB Service');
define('BOX_CLEVERREACH', 'CleverReach');
define('BOX_SUPERMAILER', 'SuperMailer');
define('BOX_OFFLINE', 'Shop offline');
define('BOX_LOGS', 'Logfiles');
define('BOX_SHIPCLOUD', 'shipcloud');
define('BOX_SHIPCLOUD_PICKUP', 'shipcloud - pickup');
define('BOX_PRODUCTS_TAGS', 'Product features');
define('BOX_TRUSTEDSHOPS', 'Trusted Shops');
define('BOX_NEWSLETTER_RECIPIENTS', 'Newsletter recipients');
define('BOX_DSGVO_EXPORT', 'GDPR Export');
define('BOX_SUPPORT', 'Support');
define('BOX_CACHING', 'Caching');
define('BOX_COOKIE_CONSENT', 'Cookie Consent');
define('BOX_SEMKNOX', 'Site Search 360 Product Search');
define('BOX_PAGES_CONTENT', 'Content sites');
define('BOX_PRODUCTS_CONTENT', 'Product attachments');
define('BOX_CONTENT_CONTENT', 'Content attachments');
define('BOX_EMAIL_CONTENT', 'E-Mail attachments');
define('BOX_DHL', 'DHL Shipping &amp; Label Creation');
define('BOX_SCHEDULED_TASKS', 'Scheduled Tasks');
define('BOX_AVALEX', 'avalex Legal Documents');
define('BOX_GOOGLE_ANALYTICS', 'Google Analytics');
define('BOX_MATOMO_ANALYTICS', 'Matomo Analytics');
define('BOX_FACEBOOK_PIXEL', 'Facebook Pixel');
define('BOX_MODULE_CATEGORIES', 'Categories');
define('BOX_MODULE_CHECKOUT', 'Checkout');
define('BOX_MODULE_MAIN', 'Main');
define('BOX_MODULE_ORDER', 'Orders');
define('BOX_MODULE_PRODUCT', 'Products');
define('BOX_MODULE_SHOPPING_CART', 'Shopping Cart');
define('BOX_MODULE_XTCPRICE', 'Prices');

define('TXT_GROUPS','<b>Groups</b>:');
define('TXT_SYSTEM','System');
define('TXT_CUSTOMERS','Customers/Orders');
define('TXT_PRODUCTS','Products/Categories');
define('TXT_STATISTICS','Statistics');
define('TXT_TOOLS','Tools');
define('TEXT_ACCOUNTING','Admin-access for:');

// javascript messages
define('JS_ERROR', 'Error have occured during the process of your form!\nPlease make the following corrections:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* The new product attribute needs a price value\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* The new product attribute needs a price prefix (+/-)\n');

define('JS_PRODUCTS_NAME', '* The new product needs a name\n');
define('JS_PRODUCTS_DESCRIPTION', '* The new product needs a description\n');
define('JS_PRODUCTS_PRICE', '* The new product needs a price value\n');
define('JS_PRODUCTS_WEIGHT', '* The new product needs a weight value\n');
define('JS_PRODUCTS_QUANTITY', '* The new product needs a quantity value\n');
define('JS_PRODUCTS_MODEL', '* The new product needs a model value\n');
define('JS_PRODUCTS_IMAGE', '* The new product needs an image value\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* A new price for this product needs to be set\n');

define('JS_GENDER', '* The \'Salutation\' value must be chosen.\n');
define('JS_FIRST_NAME', '* The \'First Name\' entry must have at least ' . (defined('ENTRY_FIRST_NAME_MIN_LENGTH') ? ENTRY_FIRST_NAME_MIN_LENGTH : 0) . ' characters.\n');
define('JS_LAST_NAME', '* The \'Last Name\' entry must have at least ' . (defined('ENTRY_LAST_NAME_MIN_LENGTH') ? ENTRY_LAST_NAME_MIN_LENGTH : 0) . ' characters.\n');
define('JS_DOB', '* The \'Date of Birth\' entry must be in the format: xx/xx/xxxx (date/month/year).\n');
define('JS_EMAIL_ADDRESS', '* The \'E-Mail Address\' entry must have at least ' . (defined('ENTRY_EMAIL_ADDRESS_MIN_LENGTH') ? ENTRY_EMAIL_ADDRESS_MIN_LENGTH : 0) . ' characters.\n');
define('JS_ADDRESS', '* The \'Street Address\' entry must have at least ' . (defined('ENTRY_STREET_ADDRESS_MIN_LENGTH') ? ENTRY_STREET_ADDRESS_MIN_LENGTH : 0) . ' characters.\n');
define('JS_POST_CODE', '* The \'Postcode\' entry must have at least ' . (defined('ENTRY_POSTCODE_MIN_LENGTH') ? ENTRY_POSTCODE_MIN_LENGTH : 0) . ' characters.\n');
define('JS_CITY', '* The \'City\' entry must have at least ' . (defined('ENTRY_CITY_MIN_LENGTH') ? ENTRY_CITY_MIN_LENGTH : 0) . ' characters.\n');
define('JS_STATE', '* The \'State\' entry must be selected.\n');
define('JS_STATE_SELECT', '-- Select above --');
define('JS_ZONE', '* The \'State\' entry must be selected from the list for this country.');
define('JS_COUNTRY', '* The \'Country\' value must be chosen.\n');
define('JS_TELEPHONE', '* The \'Telephone Number\' entry must have at least ' . (defined('ENTRY_TELEPHONE_MIN_LENGTH') ? ENTRY_TELEPHONE_MIN_LENGTH : 0) . ' characters.\n');
define('JS_PASSWORD', '* The \'Password\' and \'Confirmation\' entries must match and have at least ' . (defined('ENTRY_PASSWORD_MIN_LENGTH') ? ENTRY_PASSWORD_MIN_LENGTH : 0) . ' characters.\n');

define('JS_ORDER_DOES_NOT_EXIST', 'Order Number %s does not exist!');

define('CATEGORY_PERSONAL', 'Personal');
define('CATEGORY_ADDRESS', 'Address');
define('CATEGORY_CONTACT', 'Contact');
define('CATEGORY_COMPANY', 'Company');
define('CATEGORY_OPTIONS', 'More Options');

define('ENTRY_GENDER', 'Salutation:');
define('ENTRY_GENDER_ERROR', '');
define('ENTRY_FIRST_NAME', 'First Name:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_FIRST_NAME_MIN_LENGTH') ? ENTRY_FIRST_NAME_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_LAST_NAME', 'Last Name:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_LAST_NAME_MIN_LENGTH') ? ENTRY_LAST_NAME_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_DATE_OF_BIRTH', 'Date of Birth:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<span class="errorText">(e.g. 21/05/1970)</span>');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail Address:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_EMAIL_ADDRESS_MIN_LENGTH') ? ENTRY_EMAIL_ADDRESS_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<span class="errorText">Invalid E-Mail Address! (We currently don\'t support german umlauts in e-mail addresses.)</span>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<span class="errorText">This e-mail address already exists!</span>');
define('ENTRY_COMPANY', 'Company name:');
define('ENTRY_STREET_ADDRESS', 'Street Address:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_STREET_ADDRESS_MIN_LENGTH') ? ENTRY_STREET_ADDRESS_MIN_LENGTH : 0) . ' Chars</span>');
define('ENTRY_SUBURB', 'Addition to address:');
define('ENTRY_POST_CODE', 'Postcode:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_POSTCODE_MIN_LENGTH') ? ENTRY_POSTCODE_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_CITY', 'City:');
define('ENTRY_CITY_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_CITY_MIN_LENGTH') ? ENTRY_CITY_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_STATE', 'State:');
define('ENTRY_STATE_ERROR', '&nbsp;<span class="errorText">required</font></small>');
define('ENTRY_COUNTRY', 'Country:');
define('ENTRY_COUNTRY_ERROR', 'Please choose your country.');
define('ENTRY_TELEPHONE_NUMBER', 'Telephone Number:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<span class="errorText">min. ' . (defined('ENTRY_TELEPHONE_MIN_LENGTH') ? ENTRY_TELEPHONE_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_FAX_NUMBER', 'Fax Number:');
define('ENTRY_NEWSLETTER', 'Newsletter:');
define('ENTRY_CUSTOMERS_STATUS', 'Customer status:');
define('ENTRY_NEWSLETTER_YES', 'Subscribed');
define('ENTRY_NEWSLETTER_NO', 'Unsubscribed');
define('ENTRY_MAIL_ERROR','&nbsp;<span class="errorText">Please choose an option</span>');
define('ENTRY_PASSWORD','Password (generated)');
define('ENTRY_PASSWORD_ERROR','&nbsp;<span class="errorText">min. ' . (defined('ENTRY_PASSWORD_MIN_LENGTH') ? ENTRY_PASSWORD_MIN_LENGTH : 0) . ' chars</span>');
define('ENTRY_MAIL_COMMENTS','additional e-mail text:');

define('ENTRY_MAIL','Send e-mail with password to customer?');
define('YES','yes');
define('NO','no');
define('SAVE_ENTRY','Save changes?');
define('TEXT_CHOOSE_INFO_TEMPLATE','Template for product details');
define('TEXT_CHOOSE_OPTIONS_TEMPLATE','Template for product options');
define('TEXT_SELECT','-- Please Select --');
define('PULL_DOWN_DEFAULT', 'Please Select');

// BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons
// Icons
define('ICON_ARROW_RIGHT','marked');
define('ICON_BIG_WARNING','Attention!');
define('ICON_CROSS', 'False');
define('ICON_CURRENT_FOLDER', 'Current Folder');
define('ICON_DELETE', 'Delete');
define('ICON_EDIT','Edit');
define('ICON_ERROR', 'Error');
define('ICON_FILE', 'File');
define('ICON_FILE_DOWNLOAD', 'Download');
define('ICON_FOLDER', 'Folder');
define('ICON_LOCKED', 'Locked');
define('ICON_POPUP','Banner Preview');
define('ICON_PREVIOUS_LEVEL', 'Previous Level');
define('ICON_PREVIEW', 'Preview');
define('ICON_STATISTICS', 'Statistics');
define('ICON_SUCCESS', 'Success');
define('ICON_TICK', 'True');
define('ICON_UNLOCKED', 'Unlocked');
define('ICON_WARNING', 'Warning');
// EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Page %s of %d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Banners)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Countries)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Customers)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Currencies)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Languages)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Manufacturers)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Newsletters)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS_RECIPIENTS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Newsletter recipients)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Orders)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Orders Status)');
define('TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Cross-sell groups)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Packing Units)');
define('TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Shippingstatus)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Products)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> products expected)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Reviews)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> products on special)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Tax Classes)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Tax Zones)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Tax Rates)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> zones)');
define('TEXT_DISPLAY_NUMBER_OF_WHOS_ONLINE', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Customers who are online)');

define('PREVNEXT_BUTTON_PREV', '&laquo;');
define('PREVNEXT_BUTTON_NEXT', '&raquo;');

define('TEXT_DEFAULT', 'Default');
define('TEXT_SET_DEFAULT', 'Set as default');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Required</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Error: There is currently no default currency set. Please set one at: Zones/Tax -> Currencies');
define('ERROR_NO_DEFAULT_LANGUAGE_DEFINED', 'Error: There is currently no default language set. Please set one at: Zones/Tax -> Languages');

define('TEXT_CACHE_CATEGORIES', 'Categories Box');
define('TEXT_CACHE_MANUFACTURERS', 'Manufacturers Box');
define('TEXT_CACHE_ALSO_PURCHASED', 'Also Purchased Module');

define('TEXT_NONE', '-- none --');
define('TEXT_AUTO_PROPORTIONAL', '-- auto proportional --');
define('TEXT_AUTO_MAX', '-- auto max --');
define('TEXT_TOP', 'Top');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Error: Destination does not exist.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Error: Destination is not writeable.');
define('ERROR_FILE_NOT_SAVED', 'Error: File upload not saved.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Error: File upload type not allowed.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Success: File upload saved successfully.');
define('WARNING_NO_FILE_UPLOADED', 'Warnung: No file uploaded.');
define('ERROR_FILE_NOT_REMOVEABLE', 'Error: File not removed.');

define('DELETE_ENTRY','Delete entry?');
define('TEXT_PAYMENT_ERROR','<b>WARNING:</b> Please activate a <a href="%s">Payment Method</a>!');
define('TEXT_SHIPPING_ERROR','<b>WARNING:</b> Please activate a <a href="%s">Shipping Method</a>!');
define('TEXT_PAYPAL_CONFIG','<b>WARNING:</b> Please configure the PayPal payment settings for "Live mode" here: <a href="%s"><strong>Partner -> PayPal</strong></a>.'); //DokuMan - 2012-05-31 - show warning if PayPal payment module activated, but not configured for live mode yet
define('TEXT_DUPLICATE_CONFIG_ERROR','<b>WARNING:</b> Duplicate configuration key: ');

define('TEXT_NETTO','net: ');

define('ENTRY_CID','Customer ID:');
define('IP','Order IP:');
define('CUSTOMERS_MEMO','Memos:');
define('DISPLAY_MEMOS','Show/Write');
define('TITLE_MEMO','Customer MEMO');
define('ENTRY_LANGUAGE','Language:');
define('CATEGORIE_NOT_FOUND','Category not found!');

// BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons
// Image Icons
define('IMAGE_RELEASE', 'Redeem Gift Voucher');
define('IMAGE_ICON_STATUS_GREEN_STOCK','in stock');
define('IMAGE_ICON_STATUS_GREEN','active');
define('IMAGE_ICON_STATUS_GREEN_LIGHT','activate');
define('IMAGE_ICON_STATUS_RED','inactive');
define('IMAGE_ICON_STATUS_RED_LIGHT','deactivate');
define('IMAGE_ICON_INFO','select');
// EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons

define('_JANUARY', 'January');
define('_FEBRUARY', 'February');
define('_MARCH', 'March');
define('_APRIL', 'April');
define('_MAY', 'May');
define('_JUNE', 'June');
define('_JULY', 'July');
define('_AUGUST', 'August');
define('_SEPTEMBER', 'September');
define('_OCTOBER', 'October');
define('_NOVEMBER', 'November');
define('_DECEMBER', 'December');

// Beschreibung f&uuml;r Abmeldelink im Newsletter
define('TEXT_NEWSLETTER_REMOVE', 'To unsubscribe from a newsletter, click here:');

define('TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> gift vouchers)');
define('TEXT_DISPLAY_NUMBER_OF_COUPONS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> coupons)');
define('TEXT_VALID_PRODUCTS_LIST', 'Products List');
define('TEXT_VALID_PRODUCTS_ID', 'Products ID');
define('TEXT_VALID_PRODUCTS_NAME', 'Products Name');
define('TEXT_VALID_PRODUCTS_MODEL', 'Products Model');

define('TEXT_VALID_CATEGORIES_LIST', 'Categories List');
define('TEXT_VALID_CATEGORIES_ID', 'Category ID');
define('TEXT_VALID_CATEGORIES_NAME', 'Category Name');

define('TXT_ALL','All');

// UST ID
define('HEADING_TITLE_VAT','Vat-ID');
define('ENTRY_VAT_ID','Vat-ID');
define('ENTRY_CUSTOMERS_VAT_ID', 'Vat-ID:');
define('TEXT_VAT_FALSE','<span class="messageStackError">Checked/VAT is invalid!</span>');
define('TEXT_VAT_TRUE','<span class="messageStackSuccess">Checked/VAT is valid!</span>');
define('TEXT_VAT_UNKNOWN_COUNTRY','<span class="messageStackError">Not Checked/Unknown country!</span>');
define('TEXT_VAT_INVALID_INPUT','<span class="messageStackError">Not Checked/The provided CountryCode is invalid or the VAT number is empty!</span>');
define('TEXT_VAT_SERVICE_UNAVAILABLE','<span class="messageStackError">Not Checked/The SOAP service is unavailable, try again later!</span>');
define('TEXT_VAT_MS_UNAVAILABLE','<span class="messageStackError">Not Checked/The Member State service is unavailable, try again later or with another Member State!</span>');
define('TEXT_VAT_TIMEOUT','<span class="messageStackError">Not Checked/The Member State service could not be reached in time, try again later or with another Member State!</span>');
define('TEXT_VAT_SERVER_BUSY','<span class="messageStackError">Not Checked/The service cannot process your request. Try again later!</span>');
define('TEXT_VAT_NO_PHP5_SOAP_SUPPORT','<span class="messageStackError">Not Checked/Your System lacks PHP5 SOAP support!</span>');
define('TEXT_VAT_CONNECTION_NOT_POSSIBLE','<span class="messageStackError">ERROR: Connection to webservice not possible (SOAP-ERROR)!</span>');

define('ERROR_GIF_MERGE','Missing GDlib Gif-Support, merge failed');
define('ERROR_GIF_UPLOAD','Missing GDlib Gif-Support, processing of Gif image failed');

// BOF - Tomcraft - 2009-06-17 Google Sitemap
define('BOX_GOOGLE_SITEMAP', 'Google Sitemap');
// EOF - Tomcraft - 2009-06-17 Google Sitemap

// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
define('BOX_PAYPAL','PayPal');
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul

// BOF - Dokuman - 2009-10-02 - added moneybookers payment module version 2.4
define('_PAYMENT_MONEYBOOKERS_EMAILID_TITLE','Skrill email address');
define('_PAYMENT_MONEYBOOKERS_EMAILID_DESC','Email address you have registered with Skrill.<br />If not having a Skrill account you may get one for free at <a href="https://account.skrill.com/signup/page1" target="_blank">Skrill.com</a>.');
define('_PAYMENT_MONEYBOOKERS_MERCHANTID_TITLE','Merchant ID');
define('_PAYMENT_MONEYBOOKERS_MERCHANTID_DESC','Your Skrill Merchant ID');
define('_PAYMENT_MONEYBOOKERS_PWD_TITLE','Skrill Secret Word');
define('_PAYMENT_MONEYBOOKERS_PWD_DESC','The secret word can be found in your Skrill profile (this is not your password!). It must be lower-case characters and numbers only.<br />To activate the processing at Skrill: Send an email including your Skrill email address and shopsystems domain name<br />To: <a href="mailto:ecommerce@skrill.com?subject=modified eCommerce Shopsoftware: Activation of Skrill Quick Checkout">ecommerce@skrill.com</a>');
define('_PAYMENT_MONEYBOOKERS_TMP_STATUS_ID_TITLE','order status - temporary order');
define('_PAYMENT_MONEYBOOKERS_TMP_STATUS_ID_DESC','A temporary order is created after submitting "send order" during order process. If problems occur you find an order with this status.');
define('_PAYMENT_MONEYBOOKERS_PROCESSED_STATUS_ID_TITLE','order status - payment OK');
define('_PAYMENT_MONEYBOOKERS_PROCESSED_STATUS_ID_DESC','If everything is fine and transaction confirmed by Skrill.');
define('_PAYMENT_MONEYBOOKERS_PENDING_STATUS_ID_TITLE','order status - payment on hold');
define('_PAYMENT_MONEYBOOKERS_PENDING_STATUS_ID_DESC','If someone does not have balance at his Skrill account. The payment is pending until Skrill receive settlement.');
define('_PAYMENT_MONEYBOOKERS_CANCELED_STATUS_ID_TITLE','order status - payment cancelled');
define('_PAYMENT_MONEYBOOKERS_CANCELED_STATUS_ID_DESC','If a (credit card) payment is rejected.');
define('MB_ERROR_NO_MERCHANT','There is no Skrill account associated with this email address.');
define('MB_MERCHANT_OK','Skrill account OK, merchant ID %s received and saved.');
define('MB_INFO','<img src="../images/icons/moneybookers/MBbanner.jpg"><br /><br />You may accept credit cards, debit notes, online bank transfer, Giropay and other major local payment payments after one single activation. You do not need the hassle of contracts with every payment processor if going with Skrill. All is done with <a href="https://account.skrill.com/signup/page1" target="_blank"><b>free Skrill account</b></a>. Additional payment options are free of charge, and there are <b>no monthy fees or activation costs</b>.<br /><br /><b>Your advantage:</b><br />- more sales by accepting all major payments<br />- reduced costs and effort - just one contract<br />- easy processing for your customer - direct payment without need to create an extra account<br />- one click activation and integration<br />- good <a href="https://www.skrill.com/en/fees/" target="_blank"><b>conditions</b></a><br />- instant payment notification and proof of customer details<br />- no extra costs, even abroad<br />- 11 mio. customers worldwide');
// EOF - Dokuman - 2009-10-02 - added moneybookers payment module version 2.4

// BOF - Tomcraft - 2009-11-02 - set global customers-group-permissions
define('BOX_CUSTOMERS_GROUP','CG-authorizations');
// EOF - Tomcraft - 2009-11-02 - set global customers-group-permissions

// BOF - Tomcraft - 2009-11-02 - New admin top menu
define('TEXT_ADMIN_START', 'Home');
define('BOX_HEADING_CONFIGURATION2','Adv. Configuration');
// EOF - Tomcraft - 2009-11-02 - New admin top menu

//BOF - web28 - 2010-04-10 - ADMIN SEARCH BAR
define('ASB_QUICK_SEARCH_CUSTOMER','Search customers...');
define('ASB_QUICK_SEARCH_ORDER','Search orders...');
define('ASB_QUICK_SEARCH_ORDER_ID','Search orders ID...');
define('ASB_QUICK_SEARCH_ARTICLE','Search products/categories...');
define('ASB_QUICK_SEARCH_EMAIL', 'Search E-Mail Addresses...');
define('ASB_QUICK_SEARCH_ARTICLE_ID','Search products/categories ID...');
define('ASB_QUICK_SEARCH_ORDER_OR_INVOICE','Search orders ID/invoice No....');
//EOF - web28 - 2010-04-10 - ADMIN SEARCH BAR

//BOF - web28 - 2010.05.30 - accounting - set all checkboxes , countries - set all flags
define('BUTTON_SET','Check All');
define('BUTTON_UNSET','Uncheck All');
//EOF - web28 - 2010.05.30 - accounting - set all checkboxes 

//BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
define('TEXT_ROWS','Row');
define('TABLE_HEADING_RESET','Reset statistics');
//EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics

//BOF - web28 - 2010-11-13 - add BUTTON_CLOSE_WINDOW
define('BUTTON_CLOSE_WINDOW' , 'Close Window');
//EOF - web28 - 2010-11-13 - add BUTTON_CLOSE_WINDOW

//BOF - hendrik - 2011-05-14 - independent invoice number and date
define('ENTRY_INVOICE_NUMBER',  'Invoice number:'); 
define('ENTRY_INVOICE_DATE',    'Invoice date:'); 
//EOF - hendrik - 2011-05-14 - independent invoice number and date 

//BOF - web28 - 2010-07-06 - added missing error text
define('ENTRY_VAT_ERROR', '&nbsp;<span class="errorText">OUT OF RANGE VAT Reg.</span>');
//EOF - web28 - 2010-07-06 - added missing error text

define ('CONFIG_INT_VALUE_ERROR', '"% s" ERROR: Please enter numbers only input was ignored% s');
define ('CONFIG_MAX_VALUE_WARNING', '"% s" WARNING:% s input was ignored [maximum:% s]');
define ('CONFIG_MIN_VALUE_WARNING', '"% s" WARNING:% s input was ignored [Minimum:% s]');

define ('WHOS_ONLINE_TIME_LAST_CLICK_INFO', 'Display period in seconds:% s After this time, the entries will be deleted.');

define('TEXT_GLOBAL_PRODUCTS_MODEL', 'Products Model');

define ('TEXT_INFO_MODULE_RESTORE', 'Do you want to restore the saved settings?<br /><br /><b>WARNING</b>: This will overwrite any current settings!');
define ('TEXT_INFO_MODULE_REMOVE', 'Do you want to uninstall the module?<br /><br /><b>WARNING</b>: All module settings are also deleted!');
define ('TEXT_INFO_MODULE_BACKUP', 'Do you want to backup the module settings?');
define ('MODULE_BACKUP_CONFIRM', 'The module settings were backuped successfully!');
define ('MODULE_RESTORE_CONFIRM', 'The module settings were restored successfully!');
define ('MODULE_UPDATE_CONFIRM', 'The module settings were updated successfully!');

/* magnalister v1.0.0 */
define('BOX_HEADING_MAGNALISTER', 'magnalister');
define('BOX_MAGNALISTER', 'magnalister Admin');
/* END magnalister */

define('CHARS_LEFT', 'chars left');
define('CHARS_MAX', 'of max.');

define('DISPLAY_PER_PAGE', 'Display per page: ');

define('SPECIALS_DATE_START_TT', 'Offers start at 00:00:00');
define('SPECIALS_DATE_END_TT', 'Offers end at midnight (23:59:59)');

define('BOX_PARCEL_CARRIERS', 'Parcel carriers');
define('TEXT_DISPLAY_NUMBER_OF_CARRIERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> carriers)');

define('RSS_FEED_TITLE', 'Latest informations from modified eCommerce Shopsoftware Blog');
define('RSS_FEED_DESCRIPTION', 'Latest informations from modified eCommerce Shopsoftware Support Forum');
define('RSS_FEED_LINK', 'http://www.modified-shop.org/blog');
define('RSS_FEED_ALTERNATIVE', 'Unfortunately the latest news cannot be displayed in the RSS feed. Please visit our Blog at <a href="'.RSS_FEED_LINK.'">www.modified-shop.org/blog</a> to get important informations for shop maintainers concerning the following topics: <ul><li>Important Updates and Fixes</li><li>Function extensions</li><li>jurisdictions</li><li>News</li><li>Gossip</li></ul>');
define('RSS_FEED_NOT_REACHABLE', 'The newsfeed could not be updated for a long time.<br>Do not miss any important news, offers and much more. Visit us <a target="_blank" href="https://www.modified-shop.org/blog">www.modified-shop.org/blog</a><br>');
define('TEXT_DISPLAY_NUMBER_OF_NEWSFEED', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> News)');

define('CFG_TXT_YES', 'Yes');
define('CFG_TXT_NO', 'No');
define('CFG_TXT_OR', 'or');
define('CFG_TXT_AND', 'and');
define('CFG_TXT_ASC', 'ascending');
define('CFG_TXT_DESC', 'descending');
define('CFG_TXT_PRODUCTS_NAME', 'Product name');
define('CFG_TXT_PRODUCTS_MODEL', 'Product model');
define('CFG_TXT_DATE_EXPECTED', 'Date Expected');
define('CFG_TXT_ACCOUNT', 'Account');
define('CFG_TXT_GUEST', 'Guest');
define('CFG_TXT_BOTH', 'both');
define('CFG_TXT_NONE', 'none');
define('CFG_TXT_ADMIN', 'admin');
define('CFG_TXT_ALL', 'all');
define('CFG_TXT_WEIGHT', 'Weight');
define('CFG_TXT_PRICE', 'Price');
define('CFG_TXT_ITEM', 'Item');
define('CFG_TXT_WHOS_ONLINE', 'Online');
define('CFG_TXT_ORDERS', 'Orders');
define('CFG_TXT_CUSTOMERS', 'Customers');
define('CFG_TXT_SALES_REPORT', 'Statistics');
define('CFG_TXT_BLOG', 'Blog');
define('CFG_TXT_P.PRODUCTS_PRICE', 'Price');
define('CFG_TXT_PD.PRODUCTS_NAME', 'Product name');
define('CFG_TXT_P.PRODUCTS_DATE_ADDED', 'Release date');
define('CFG_TXT_P.PRODUCTS_MODEL', 'Products Model');
define('CFG_TXT_P.PRODUCTS_ORDERED', 'Products ordered');
define('CFG_TXT_P.PRODUCTS_SORT', 'Sort order');
define('CFG_TXT_P.PRODUCTS_WEIGHT', 'Weight');
define('CFG_TXT_P.PRODUCTS_QUANTITY', 'On Stock');
define('CFG_TXT_S.SPECIALS_DATE_ADDED', 'Release date');

define('CSRF_TOKEN_MANIPULATION', 'CSRFToken manipulation (Due to security aspects it is not allowed to work in the admin area in different browser tabs anymore.)');
define('CSRF_TOKEN_NOT_DEFINED', 'CSRFToken not defined (Due to security aspects it is not allowed to work in the admin area in different browser tabs anymore.)');

define('TEXT_ACCOUNTING_INFO','The main admin [1] cannot be deprived of the access rights.');

define('JAVASCRIPT_DISABLED_INFO', 'JavaScript seem to be disabled in your browser. Enable JavaScript to be able to use all functions of this site and all site content');

define('BOX_MODULE_TYPE', 'Class Extensions');

define('MULTIPLE_INSTALLATION', '<span style="color:red">[Multiple Installation: %s]</span>');

define('FILEUPLOAD_INPUT_TXT', 'No file');
define('FILEUPLOAD_BTN_TXT', 'Search');

define('CHECK_LABEL_PRICE', 'Check price');

define('TEXT_PAYPAL_TAB_CONFIG', 'PayPal Configuration');
define('TEXT_PAYPAL_TAB_PROFILE', 'PayPal Profile');
define('TEXT_PAYPAL_TAB_WEBHOOK', 'PayPal Webhook');
define('TEXT_PAYPAL_TAB_MODULE', 'PayPal Products');
define('TEXT_PAYPAL_TAB_INFO', 'PayPal Information');

define('TEXT_DEFAULT_SORT_ORDER_TITLE', 'Sort order');
define('TEXT_DEFAULT_SORT_ORDER_DESC', 'Order of processing. Smallest number is executed first.');
define('TEXT_DEFAULT_STATUS_TITLE', 'Enable module?');
define('TEXT_DEFAULT_STATUS_DESC', 'Modules status');

define('TEXT_HOUR', 'hour');
define('TEXT_HOURS', 'hours');

define('DELETE_LOGS_SUCCESSFUL', 'Logfiles deleted successfully.');

define('BOX_BLACKLIST_LOGS', 'Blacklist Logs');

define('CONTINUE_WITHOUT_SAVE', 'Unsaved changes will be dismissed.');

define('TEXT_SORT_ASC','ascending');
define('TEXT_SORT_DESC','descending');

define('MSRP','MSRP');
define('YOUR_PRICE','your price ');
define('UNIT_PRICE','unit price ');
define('ONLY',' Now only ');
define('FROM','from ');
define('YOU_SAVE','you save ');
define('INSTEAD','Our previous price ');
define('TXT_PER',' per ');
define('TEXT_NO_PAYMENT', 'No payment method');

define('TEXT_ERROR_EMPTY_NOT_ALLOWED', '<span class="error_message" style="display:inline-block;">Mandatory (please fill in)</span>');
