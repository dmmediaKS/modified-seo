<?php
/* -----------------------------------------------------------------------------------------
   $Id: english.php 16320 2025-02-11 17:00:32Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.119 2003/05/19); www.oscommerce.com
   (c) 2003 nextcommerce (german.php,v 1.25 2003/08/25); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/*
 *
 *  DATE / TIME
 *
 */
 
define('HTML_PARAMS','dir="ltr" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml"');
@setlocale(LC_TIME, 'en_GB.UTF-8', 'en_GB@euro', 'en_GB', 'en-GB', 'en', 'en_GB.ISO_8859-1', 'English','en_GB.ISO_8859-15');

define('DATE_LOCALE', 'en_GB');
define('DATE_FORMAT_SHORT', 'd/m/Y');
define('DATE_FORMAT_LONG', 'l d F, Y');
define('DATE_FORMAT', DATE_FORMAT_SHORT);
define('PHP_DATE_TIME_FORMAT',  DATE_FORMAT_SHORT . ' H:i:s');
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' H:i:s');
define('DOB_FORMAT_STRING', 'dd/mm/jjjj');
 
function xtc_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

require_once(DIR_FS_INC.'auto_include.inc.php');
foreach(auto_include(DIR_WS_LANGUAGES.'english/extra/','php') as $file) require ($file);

define('TITLE', STORE_NAME);
define('HEADER_TITLE_TOP', 'Main page');
define('HEADER_TITLE_CATALOG', 'Catalogue');

// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency when changing language, 
// instead of staying with the applications default currency
defined('LANGUAGE_CURRENCY') OR define('LANGUAGE_CURRENCY', 'EUR');

define('GENDER_NONE', 'none');
define('GENDER_MALE', 'Mr.');
define('GENDER_FEMALE', 'Ms./Mrs.');
define('GENDER_DIVERSE', 'Diverse');

/*
 *
 *  BOXES
 *
 */

// text for gift voucher redeeming
define('IMAGE_REDEEM_GIFT','Redeem Gift Voucher!');

define('BOX_TITLE_STATISTICS','Statistics:');
define('BOX_ENTRY_CUSTOMERS','Customers:');
define('BOX_ENTRY_PRODUCTS','Products:');
define('BOX_ENTRY_REVIEWS','Reviews:');
define('TEXT_VALIDATING','Not validated');

// manufacturer box text
define('BOX_MANUFACTURER_INFO_HOMEPAGE', '%s Homepage');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', 'More products');

define('BOX_HEADING_ADD_PRODUCT_ID','Add to cart');
  
define('BOX_LOGINBOX_STATUS','Customer group:');     
define('BOX_LOGINBOX_DISCOUNT','Product discount');
define('BOX_LOGINBOX_DISCOUNT_TEXT','Discount');
define('BOX_LOGINBOX_DISCOUNT_OT','');

// reviews box text in includes/boxes/reviews.php
define('BOX_REVIEWS_WRITE_REVIEW', 'Review this product!');
define('BOX_REVIEWS_NO_WRITE_REVIEW', 'No review possible.');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '%s of 5 stars!');

// pull down default text
define('PULL_DOWN_DEFAULT', 'Please choose');

// javascript messages
define('JS_ERROR', 'Missing necessary information!\nPlease fill in completely.\n\n');

define('JS_REVIEW_TEXT', '* The text must consist of at least ' . REVIEW_TEXT_MIN_LENGTH . ' characters..\n');
define('JS_REVIEW_RATING', '* Enter your review.\n');
define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* Please choose a method of payment for your order.\n');
define('JS_ERROR_SUBMITTED', 'This page has already been confirmed. Please click OK and wait until the process has finished.');
define('JS_ERROR_NO_SHIPPING_MODULE_SELECTED', '* Please choose a method of shipping for your order.\n');
define('JS_ERROR_CONDITIONS_NOT_ACCEPTED', '* Unfortunately we cannot accept your order\nunless you confirm that you have read our terms and conditions!\n\n');
define('JS_ERROR_REVOCATION_NOT_ACCEPTED', '* Unfortunately we cannot accept your order\nunless you accept that the right of withdrawal expires for virtual products!\n\n');
define('JS_ERROR_PRIVACY_NOTICE_NOT_ACCEPTED', '* Unfortunately we cannot accept your order\nunless you confirm our privacy notice!\n\n');
define('JS_REVIEW_AUTHOR', '* Please enter your name.\n\n');

define('ERROR_NO_PAYMENT_MODULE_SELECTED', 'Please choose a method of payment for your order.');
define('ERROR_NO_SHIPPING_MODULE_SELECTED', 'Please choose a method of shipping for your order.');

/*
 *
 * ACCOUNT FORMS
 *
 */

define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER_ERROR', '');
define('ENTRY_GENDER_TEXT', '');
define('ENTRY_FIRST_NAME_ERROR', 'Your first name must consist of at least  ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' characters.');
define('ENTRY_FIRST_NAME_TEXT', '*');
define('ENTRY_LAST_NAME_ERROR', 'Your last name must consist of at least ' . ENTRY_LAST_NAME_MIN_LENGTH . ' characters.');
define('ENTRY_LAST_NAME_TEXT', '*');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'Your date of birth needs to be entered in the following form DD/MM/YYYY (e.g. 21/05/1970) '); //Dokuman - 2009-06-03 - correct english date format
define('ENTRY_DATE_OF_BIRTH_TEXT', '*');
define('ENTRY_DATE_OF_BIRTH_NOTE', 'e.g. 21/05/1970');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'Your e-mail address must consist of at least  ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' characters.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'Your e-mail address entered is incorrect or already registered.');
define('ENTRY_EMAIL_ERROR_NOT_MATCHING', 'Your entered e-mail addresses do not match.'); // Hetfield - 2009-08-15 - confirm e-mail at registration
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'The e-mail address you entered already exists in our database - please correct it');
define('ENTRY_EMAIL_ADDRESS_TEXT', '*');
define('ENTRY_STREET_ADDRESS_ERROR', 'Street/No. must consist of at least ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' characters.');
define('ENTRY_STREET_ADDRESS_TEXT', '*');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE_ERROR', 'Your postcode must consist of at least ' . ENTRY_POSTCODE_MIN_LENGTH . ' characters.');
define('ENTRY_POST_CODE_TEXT', '*');
define('ENTRY_CITY_ERROR', 'City must consist of at least ' . ENTRY_CITY_MIN_LENGTH . ' characters.');
define('ENTRY_CITY_TEXT', '*');
define('ENTRY_STATE_ERROR', 'Your state must consist of at least ' . ENTRY_STATE_MIN_LENGTH . ' characters.');
define('ENTRY_STATE_ERROR_SELECT', 'Please choose your state from the list.');
define('ENTRY_STATE_TEXT', '*');
define('ENTRY_COUNTRY_ERROR', 'Please choose your country.');
define('ENTRY_COUNTRY_TEXT', '*');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'Your phone number must consist of at least ' . ENTRY_TELEPHONE_MIN_LENGTH . ' characters.');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '*');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_PASSWORD_ERROR', 'Your password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.');
define('ENTRY_PASSWORD_ERROR_MIN_LOWER', 'Password must contain at least %s lowercase characters');
define('ENTRY_PASSWORD_ERROR_MIN_UPPER', 'Password must contain at least %s uppercase characters');
define('ENTRY_PASSWORD_ERROR_MIN_NUM', 'Password must contain at least %s numbers');
define('ENTRY_PASSWORD_ERROR_MIN_CHAR', 'Password must contain at least %s non-aplhanumeric characters');
define('ENTRY_PASSWORD_ERROR_INVALID_CHAR', 'Your password contains invalid characters. Please use a different password.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'Your passwords do not match.');
define('ENTRY_PASSWORD_TEXT', '*');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_ERROR','Your current password must not be empty.');
define('ENTRY_PASSWORD_NEW_TEXT', '*');
define('ENTRY_PASSWORD_NEW_ERROR', 'Your new password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.');
define('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING', 'Your passwords do not match.');

/*
 *
 *  RESULT PAGES
 *
 */

define('TEXT_RESULT_PAGE', 'Sites:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Show <strong>%d</strong> to <strong>%d</strong> (of in total <strong>%d</strong> products)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Show <strong>%d</strong> to <strong>%d</strong> (of in total <strong>%d</strong> orders)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Show <strong>%d</strong> to <strong>%d</strong> (of in total <strong>%d</strong> reviews)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', 'Show <strong>%d</strong> to <strong>%d</strong> (of in total <strong>%d</strong> new products)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Show <strong>%d</strong> to <strong>%d</strong> (of in total <strong>%d</strong> special offers)');

/*
 *
 * SITE NAVIGATION
 *
 */

define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'previous page');
define('PREVNEXT_TITLE_NEXT_PAGE', 'next page');
define('PREVNEXT_TITLE_PAGE_NO', 'page %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Previous %d pages');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Next %d pages');

/*
 *
 * PRODUCT NAVIGATION
 *
 */

define('PREVNEXT_BUTTON_PREV', '&laquo;');
define('PREVNEXT_BUTTON_NEXT', '&raquo;');

/*
 *
 * IMAGE BUTTONS
 *
 */

define('IMAGE_BUTTON_ADD_ADDRESS', 'New address');
define('IMAGE_BUTTON_BACK', 'Back');
define('IMAGE_BUTTON_CHANGE_ADDRESS', 'Change address');
define('IMAGE_BUTTON_CHECKOUT', 'Checkout');
define('IMAGE_BUTTON_CONFIRM_ORDER', 'Buy');
define('IMAGE_BUTTON_CONTINUE', 'Next');
define('IMAGE_BUTTON_DELETE', 'Delete');
define('IMAGE_BUTTON_LOGIN', 'Login');
define('IMAGE_BUTTON_IN_CART', 'Add to cart');
define('IMAGE_BUTTON_SEARCH', 'Search');
define('IMAGE_BUTTON_UPDATE', 'Update');
define('IMAGE_BUTTON_UPDATE_CART', 'Update shopping cart');
define('IMAGE_BUTTON_WRITE_REVIEW', 'Write evaluation');
define('IMAGE_BUTTON_ADMIN', 'Admin');
define('IMAGE_BUTTON_PRODUCT_EDIT', 'Edit product');
define('IMAGE_BUTTON_SEND', 'Send'); //DokuMan - 2010-03-15 - Added button description for contact form
define('IMAGE_BUTTON_CONTINUE_SHOPPING', 'Continue shopping'); //Hendrik - 2010-11-12 - used in default template ...shopping_cart.html
define('IMAGE_BUTTON_CHECKOUT_STEP2', 'Continue to step 2');
define('IMAGE_BUTTON_CHECKOUT_STEP3', 'Continue to step 3');
define('IMAGE_BUTTON_RESULTS', 'Show results');
define('IMAGE_BUTTON_SAVE', 'Save');

define('SMALL_IMAGE_BUTTON_DELETE', 'Delete');
define('SMALL_IMAGE_BUTTON_EDIT', 'Edit');
define('SMALL_IMAGE_BUTTON_VIEW', 'View');

define('ICON_ARROW_RIGHT', 'Show more');
define('ICON_CART', 'Add to cart');
define('ICON_SUCCESS', 'Success');
define('ICON_WARNING', 'Warning');
define('ICON_ERROR', 'Error');

define('TEXT_PRINT', 'Print'); //DokuMan - 2009-05-26 - Added description for 'account_history_info.php'
define('TEXT_BUTTON_RESULTS', 'Show %s results');

define('BUTTON_RESET', 'Reset');
define('BUTTON_UPDATE', 'Update');
/*
 *
 *  GREETINGS
 *
 */

define('TEXT_GREETING_PERSONAL', 'Nice to see you again <span class="greetUser">%s!</span> Would you like to view our <a href="%s">new products</a>?');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>If you are not %s , please  <a href="%s">login</a>  with your account.</small>');
define('TEXT_GREETING_GUEST', 'Welcome  <span class="greetUser">visitor!</span> Would you like to <a href="%s">login</a>? Or would you like to create a new <a href="%s">account</a>?');

define('TEXT_SORT_PRODUCTS', 'Sorting of the items is ');
define('TEXT_DESCENDINGLY', 'descending');
define('TEXT_ASCENDINGLY', 'ascending');
define('TEXT_BY', ' after ');

define('TEXT_OF_5_STARS', '%s of 5 Stars!');
define('TEXT_REVIEW_BY', 'from %s');
define('TEXT_REVIEW_WORD_COUNT', '%s words');
define('TEXT_REVIEW_RATING', 'Review: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', 'Date added: %s');
define('TEXT_NO_REVIEWS', 'There are no reviews yet.');
define('TEXT_NO_NEW_PRODUCTS', 'There are no new products for the last '.MAX_DISPLAY_NEW_PRODUCTS_DAYS.' days. Instead of that we will show you the latest arrived products.'); 
define('TEXT_UNKNOWN_TAX_RATE', 'Unknown tax rate');

/*
 *
 * WARNINGS
 *
 */

define('WARNING_INSTALL_DIRECTORY_EXISTS', 'Warning: The installation directory is still available on: %s. Please delete this directory for security reasons!');
define('WARNING_CONFIG_FILE_WRITEABLE', 'Warning: The modified eCommerce Shopsoftware is able to write to the configuration directory: %s. That represents a possible safety hazard - please correct the user access rights for this directory!');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'Warning: Directory for sesssions doesn&acute;t exist: ' . xtc_session_save_path() . '. Sessions will not work until this directory has been created!');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'Warning: The modified eCommerce Shopsoftware is not able to write into the session directory: ' . xtc_session_save_path() . '. Sessions will not work until the user access rights for this directory have been changed!');
define('WARNING_SESSION_AUTO_START', 'Warning: session.auto_start is activated (enabled) - Please deactivate (disable) this PHP feature in php.ini and restart your web server!');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', 'Warning: Directory for article download does not exist: ' . DIR_FS_DOWNLOAD . '. This feature will not work until this directory has been created!');

define('SUCCESS_ACCOUNT_UPDATED', 'Your account has been updated successfully.');
define('SUCCESS_PASSWORD_UPDATED', 'Your password has been changed successfully!');
define('ERROR_CURRENT_PASSWORD_NOT_MATCHING', 'The entered password does not match with the stored password. Please try again.');
define('TEXT_MAXIMUM_ENTRIES', '<strong>Reference:</strong> You are able to choose out of %s entries in your address book!');
define('SUCCESS_ADDRESS_BOOK_ENTRY_DELETED', 'The selected entry has been deleted successfully.');
define('SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED', 'Your address book has been updated sucessfully!');
define('WARNING_PRIMARY_ADDRESS_DELETION', 'The standard postal address can not be deleted. Please create another address and define it as standard postal address first. Then this entry can be deleted.');
define('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY', 'This address book entry is not available.');
define('ERROR_ADDRESS_BOOK_FULL', 'Your addressbook is full. In order to add new addresses, please erase previous ones first.');
define('ERROR_CHECKOUT_SHIPPING_NO_METHOD', 'No shipping method selected.');
define('ERROR_CHECKOUT_SHIPPING_NO_MODULE', 'No shipping method available.');

//  conditions check

define('ERROR_CONDITIONS_NOT_ACCEPTED', 'Please confirm that you have read our terms and conditions to proceed with your order.');
define('ERROR_REVOCATION_NOT_ACCEPTED', 'Please accept that the right of withdrawal expires for virtual products.');
define('ERROR_PRIVACY_NOTICE_NOT_ACCEPTED', 'Please confirm that you have read our privacy notice.');

define('SUB_TITLE_OT_DISCOUNT','Discount:');

define('NOT_ALLOWED_TO_SEE_PRICES','You do not have the permission to see the prices ');
define('NOT_ALLOWED_TO_SEE_PRICES_TEXT','You do not have the permission to see the prices, please create an account.');

define('TEXT_DOWNLOAD','Download');
define('TEXT_VIEW','View');

define('TEXT_BUY', '%s x \'');
define('TEXT_NOW', '\' order');
define('TEXT_GUEST','Guest');
define('TEXT_SEARCH_ENGINE_AGENT','Search engine');

/*
 *
 * ADVANCED SEARCH
 *
 */

define('TEXT_AC_ALL_CATEGORIES', 'All');
define('TEXT_ALL_CATEGORIES', 'All categories');
define('TEXT_ALL_MANUFACTURERS', 'All manufacturers');
define('JS_AT_LEAST_ONE_INPUT', '* One of the following fields must be filled out:\n    Keywords\n    Date added from\n    Date added to\n    Price over\n    Price up to\n');
define('AT_LEAST_ONE_INPUT', 'One of the following fields must be filled out:<br />keywords consisting at least 3 characters<br />Price over<br />Price up to<br />');
define('TEXT_SEARCH_TERM','Your search for: ');
define('JS_INVALID_FROM_DATE', '* Invalid from date\n');
define('JS_INVALID_TO_DATE', '* Invalid up to Date\n');
define('JS_TO_DATE_LESS_THAN_FROM_DATE', '* The from date must be larger or same size as up to now\n');
define('JS_PRICE_FROM_MUST_BE_NUM', '* Price over, must be a number\n');
define('JS_PRICE_TO_MUST_BE_NUM', '* Price up to, must be a number\n');
define('JS_PRICE_TO_LESS_THAN_PRICE_FROM', '* Price up to must be larger or same size as Price over.\n');
define('JS_INVALID_KEYWORDS', '* Invalid search key\n');
define('TEXT_LOGIN_ERROR', '<strong>ERROR:</strong> The entered \'e-mail address\' and/or the \'password\' do not match.');
define('TEXT_RELOGIN_NEEDED', 'Please sign in again.');
//define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<span class="color_error_message"><strong>WARNING:</strong></span> The e-mail address entered is not registered. Please try again.'); // Not used anymore as we do not give a hint that an e-mail address is or is not in the database!
define('TEXT_PASSWORD_SENT', 'A new password was sent by e-mail.');
define('TEXT_PRODUCT_NOT_FOUND', 'Product not found');
define('TEXT_MORE_INFORMATION', 'For further information, please visit the <a href="%s" onclick="window.open(this.href); return false;">homepage</a> of this product.');
define('TEXT_DATE_ADDED', 'This Product was added to our catalogue on %s.');
define('TEXT_DATE_AVAILABLE', '<span class="color_error_message">This Product is expected to be on stock again on %s </span>');
define('SUB_TITLE_SUB_TOTAL', 'Sub-total:');

define('OUT_OF_STOCK_CANT_CHECKOUT', 'The products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' , are not available in the requested quantity.<br />Please decrease quantity for marked products. Thank you');
define('OUT_OF_STOCK_CAN_CHECKOUT', 'The products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' , are not available in the requested quantity.<br />We will restock the products currently out of stock as soon as possible. Partial delivery upon request.');

define('MINIMUM_ORDER_VALUE_NOT_REACHED_1', 'You need to reach the minimum order value of: ');
define('MINIMUM_ORDER_VALUE_NOT_REACHED_2', ' <br />Please increase order value by at least: ');
define('MAXIMUM_ORDER_VALUE_REACHED_1', 'You ordered more than the allowed amount of: ');
define('MAXIMUM_ORDER_VALUE_REACHED_2', '<br /> Please decrease your order by at least: ');

define('ERROR_INVALID_PRODUCT', 'The product chosen was not found!');
define('JS_KEYWORDS_MIN_LENGTH', 'The search term must be at least ' . (int)SEARCH_MIN_LENGTH . ' characters long.\n');

define('TEXT_SEARCH_HELP', 'You can search by products name%sand article numbers.<br /><br />Use logical connectives such as "AND" and "OR".<br />Example: "Screen AND Speaker" will only return results containing BOTH words "Screen" and "Speaker".<br /><br />You can search for exact phrases by using quotation marks " ".<br />Example: "USB cable".<br /><br />Use brackets for interleaving.<br />Example: screen AND (speaker OR Keyboard or "USB cable").');
define('TEXT_SEARCH_MANUFACTURERS', 'manufacturers');
define('TEXT_SEARCH_DESCRIPTION', 'description');
define('TEXT_SEARCH_ATTRIBUTES', 'attributes');
define('TEXT_SEARCH_TAGS', 'tags');

/*
 *
 * NAVBAR TITLE
 *
 */

define('NAVBAR_TITLE_ACCOUNT', 'Your account');
define('NAVBAR_TITLE_1_ACCOUNT_EDIT', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_EDIT', 'Changing your personal data');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY', 'Your completed orders');
define('NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO', 'Completed orders');
define('NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO', 'Order number %s');
define('NAVBAR_TITLE_1_ACCOUNT_PASSWORD', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_PASSWORD', 'Change password');
define('NAVBAR_TITLE_1_ADDRESS_BOOK', 'Your account');
define('NAVBAR_TITLE_2_ADDRESS_BOOK', 'Address book');
define('NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS', 'Your account');
define('NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS', 'Address book');
define('NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS', 'New entry');
define('NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS', 'Change entry');
define('NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS', 'Delete Entry');
define('NAVBAR_TITLE_ADVANCED_SEARCH', 'Advanced Search');
define('NAVBAR_TITLE1_ADVANCED_SEARCH', 'Advanced Search');
define('NAVBAR_TITLE2_ADVANCED_SEARCH', 'Search results');
define('NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION', 'Confirmation');
define('NAVBAR_TITLE_1_CHECKOUT_PAYMENT', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_PAYMENT', 'Method of payment');
define('NAVBAR_TITLE_1_PAYMENT_ADDRESS', 'Checkout');
define('NAVBAR_TITLE_2_PAYMENT_ADDRESS', 'Change billing address');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING', 'Shipping information');
define('NAVBAR_TITLE_1_CHECKOUT_SHIPPING_ADDRESS', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_SHIPPING_ADDRESS', 'Change shipping address');
define('NAVBAR_TITLE_1_CHECKOUT_SUCCESS', 'Checkout');
define('NAVBAR_TITLE_2_CHECKOUT_SUCCESS', 'Success');
define('NAVBAR_TITLE_CREATE_ACCOUNT', 'Create account');
define('NAVBAR_TITLE_LOGIN', 'Login');
define('NAVBAR_TITLE_LOGOFF','Good bye');
define('NAVBAR_TITLE_PRODUCTS_NEW', 'New products');
define('NAVBAR_TITLE_SHOPPING_CART', 'Shopping cart');
define('NAVBAR_TITLE_SPECIALS', 'Special offers');
define('NAVBAR_TITLE_COOKIE_USAGE', 'Cookie usage');
define('NAVBAR_TITLE_PRODUCT_REVIEWS', 'Reviews');
define('NAVBAR_TITLE_REVIEWS_WRITE', 'Opinions');
define('NAVBAR_TITLE_REVIEWS','Reviews');
define('NAVBAR_TITLE_SSL_CHECK', 'Note on safety');
define('NAVBAR_TITLE_CREATE_GUEST_ACCOUNT','Your customer address');
define('NAVBAR_TITLE_PASSWORD_DOUBLE_OPT','Password forgotten?');
define('NAVBAR_TITLE_NEWSLETTER','Newsletter');
define('NAVBAR_GV_REDEEM', 'Redeem Voucher');
define('NAVBAR_GV_SEND', 'Send Voucher');
define('NAVBAR_TITLE_DOWNLOAD', 'Downloads');

/*
 *
 *  MISC
 *
 */

define('TEXT_NEWSLETTER','You want to stay up to date?<br />No problem, receive our newsletter for the latest updates.');
define('TEXT_EMAIL_INPUT','Your e-mail address has been registered in our system.<br />An e-mail with a confirmation link has been sent out. Click the link to complete registration!');

define('TEXT_WRONG_CODE','The security code you entered was not correct. Please try again. <br />The form is not case sensitive.');
define('TEXT_EMAIL_EXIST_NO_NEWSLETTER','This e-mail address is registered but not yet activated!');
define('TEXT_EMAIL_EXIST_NEWSLETTER','This e-mail address is already registered for the newsletter!');
define('TEXT_EMAIL_NOT_EXIST','This e-mail address is not registered for newsletters!');
define('TEXT_EMAIL_DEL','Your e-mail address was deleted successfully from our newsletter-database.');
define('TEXT_EMAIL_DEL_ERROR','An Error occured, your e-mail address has not been removed from our database!');
define('TEXT_EMAIL_ACTIVE','Your e-mail address has successfully been registered for the newsletter!');
define('TEXT_EMAIL_ACTIVE_ERROR','An error occured, your e-mail address has not been registered for the newsletter!');
define('TEXT_EMAIL_SUBJECT','Your newsletter account');

define('TEXT_CUSTOMER_GUEST','Guest');

define('TEXT_LINK_MAIL_SENDED','Your new password request must be confirmed.<br />An e-mail with a confirmation link has been send out. Click the link in order to complete your request.<br/><br/>The confirmation link is %s minutes valid.');
define('TEXT_PASSWORD_MAIL_SENDED','You will receive an e-mail with your new password within minutes.<br />Please change your password after your first login.');
define('TEXT_CODE_ERROR','The security code you entered was not correct.<br />Please try again.');
define('TEXT_EMAIL_ERROR','The e-mail address you entered was not correct.<br />Please try again.');
define('TEXT_NO_ACCOUNT','Your request for a new password is either invalid or timed out.<br />Please try again.');
define('HEADING_PASSWORD_FORGOTTEN','Password renewal?');
define('TEXT_PASSWORD_FORGOTTEN','Change your password in three easy steps.');
define('TEXT_EMAIL_PASSWORD_FORGOTTEN','Confirmation mail for password renewal');
define('TEXT_EMAIL_PASSWORD_NEW_PASSWORD','Your new password');
define('ERROR_MAIL','Please check the data entered in the form.');

define('TEXT_CATEGORIE_NOT_FOUND','Category not found');

define('GV_FAQ', 'Gift voucher FAQ');
define('ERROR_NO_REDEEM_CODE', 'You did not enter a redeem code.');
define('ERROR_NO_INVALID_REDEEM_GV', 'Invalid gift voucher code');
define('TABLE_HEADING_CREDIT', 'Credits available');
define('EMAIL_GV_TEXT_SUBJECT', 'A gift from %s');
define('MAIN_MESSAGE', 'You have decided to send a gift voucher worth %s to %s who\'s e-mail address is %s<br /><br />Following text will be included in the e-mail:<br /><br />Dear %s<br /><br />You have received a Gift voucher worth %s by %s');
define('REDEEMED_AMOUNT','Your gift voucher was successfully added to your account. Gift voucher amount: %s');
define('REDEEMED_COUPON','Your voucher has been successfully credited to your account and will be cashed automatically on your purchase.');
define('REDEEMED_COUPON_NO_SPECIALS','Your voucher has been successfully credited to your account and will be cashed automatically on your purchase, but can not be used for specials.');

define('ERROR_INVALID_USES_USER_COUPON','This coupon can only be redeemed ');
define('ERROR_INVALID_USES_COUPON','This coupon can only be redeemed ');
define('TIMES',' times.');
define('ERROR_INVALID_STARTDATE_COUPON','Your coupon is not available yet.');
define('ERROR_INVALID_FINISDATE_COUPON','Your coupon is already expired.');
define('ERROR_INVALID_CUSTOMERS_STATUS_COUPON','This coupon can only be redeemed by customers in a specific customer group.');
define('ERROR_INVALID_MINIMUM_ORDER_COUPON', 'This coupon can be redeemed only with a minimum order value of %s!');
define('ERROR_INVALID_MINIMUM_ORDER_COUPON_ADD','<br/>You have to enter the coupon code again when you reach the minimum order value!');
define('ERROR_COUPON_REQUIRES_ACCOUNT', 'To redeem the coupon you need a customer account.');
define('PERSONAL_MESSAGE', '%s writes:');

define('TEXT_LINK_TITLE_INFORMATION', 'Information');

/*
 *
 * CUOPON POPUP
 *
 */

define('TEXT_CLOSE_WINDOW', 'Close window [x]');
define('TEXT_COUPON_HELP_HEADER', 'Your voucher/coupon has been successfully redeemed.');
define('TEXT_COUPON_HELP_NAME', '<br /><br />Voucher/Coupon name : %s');
define('TEXT_COUPON_HELP_SPECIALS', '<br /><br />Your coupon cannot be used on special offers.');
define('TEXT_COUPON_HELP_FIXED', '<br /><br />This voucher/coupon is worth %s off your next order');
define('TEXT_COUPON_HELP_MINORDER', '<br /><br />You need to spend at least %s to be able to use the voucher.');
define('TEXT_COUPON_HELP_FREESHIP', '<br /><br />This voucher gives you free shipping on your order');
define('TEXT_COUPON_HELP_DESC', '<br /><br />Voucher description : %s');
define('TEXT_COUPON_HELP_DATE', '<br /><br />This voucher is valid from: %s to %s');
define('TEXT_COUPON_HELP_RESTRICT', '<br /><br />Product / Category Restrictions');
define('TEXT_COUPON_HELP_CATEGORIES', 'Category');
define('TEXT_COUPON_HELP_PRODUCTS', 'Product');
define('TEXT_COUPON_HELP_MANUFACTURERS', 'Manufacturers');
define('ERROR_ENTRY_AMOUNT_CHECK', 'Invalid amount');
define('ERROR_ENTRY_EMAIL_ADDRESS_CHECK', 'Invalid e-mail address');
define('TEXT_COUPON_PRODUCTS_RESTRICT', 'Your voucher/coupon is limited to some products.');
define('TEXT_COUPON_CATEGORIES_RESTRICT', 'Your voucher/coupon is limited to some categories.');
define('TEXT_COUPON_MANUFACTURERS_RESTRICT', 'Your voucher/coupon is limited to some manufacturers.');

// VAT Reg No
define('ENTRY_VAT_TEXT', '');
define('ENTRY_VAT_NOTE','for EU-Countries only');
define('ENTRY_VAT_ERROR', 'The chosen VAT Reg No is not valid or cannot be verified at the moment! Please enter a valid VAT Reg No or leave this field empty.');
define('MSRP','MSRP');
define('YOUR_PRICE','your price ');
define('UNIT_PRICE','unit price ');
define('ONLY',' Now only ');
define('FROM','from ');
define('YOU_SAVE','you save ');
define('INSTEAD','Our previous price ');
define('TXT_PER',' per ');
define('TAX_INFO_INCL','%s VAT incl.');
define('TAX_INFO_EXCL','%s VAT excl.');
define('TAX_INFO_ADD','%s VAT plus.');
define('SHIPPING_EXCL','excl.');
define('SHIPPING_INCL','incl.');
define('SHIPPING_COSTS','Shipping costs'); 

define('SHIPPING_TIME','Shipping time: ');
define('MORE_INFO','[More]');

define('ENTRY_PRIVACY_ERROR','Please confirm that you have read our privacy policy!');
define('TEXT_PAYMENT_FEE','Paymentfee');

define('_MODULE_INVALID_SHIPPING_ZONE', 'Unfortunately we do not deliver to the chosen country.');
define('_MODULE_UNDEFINED_SHIPPING_RATE', 'Shipping costs cannot be calculated at the moment, please contact us.');

define('NAVBAR_TITLE_1_ACCOUNT_DELETE', 'Your account');
define('NAVBAR_TITLE_2_ACCOUNT_DELETE', 'Delete account');	

//contact-form error messages
define('ERROR_EMAIL','<p><b>Your e-mail address:</b> None or invalid input!</p>');
define('ERROR_VVCODE','<p><b>Security code:</b> No match, please enter your security code again!</p>');
define('ERROR_MSG_BODY','<p><b>Your message:</b> No input!</p>');

//Table Header checkout_confirmation.php
define('HEADER_QTY', 'Number');
define('HEADER_ARTICLE', 'Item');    
define('HEADER_SINGLE', 'Singleprice');
define('HEADER_TOTAL','Total');
define('HEADER_MODEL', 'Model');

### PayPal API Modul
define('ERROR_ADDRESS_NOT_ACCEPTED', '* Please confirm your address so we can process your order.');
define('PAYPAL_EXP_VORL','Provisional forwarding expenses');
### PayPal API Modul

define('BASICPRICE_VPE_TEXT','in this volume only ');
define('GRADUATED_PRICE_MAX_VALUE', 'from');
define('_SHIPPING_TO', 'shipping to ');

define('ERROR_SQL_DB_QUERY','We are sorry, but an database error has occurred somewhere on this page!');
define('ERROR_SQL_DB_QUERY_REDIRECT','You will be redirected back to our home page in %s seconds!');

define('TEXT_AGB_CHECKOUT','Please take note of our General Terms & Conditions %s and Privacy Policy %s.');
define('TEXT_REVOCATION_CHECKOUT', ', Cancellation Policy %s');
define('DOWNLOAD_NOT_ALLOWED', '<h1>Forbidden</h1>This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser does not understand how to supply the credentials required.');

define('TEXT_INFO_DETAILS', ' Details');
define('TEXT_SAVED_BASKET', 'Please check your shopping cart. There are products from a last visit.');
//define('TEXT_PRODUCTS_QTY_REDUCED', 'Maximum quantity for the last added / updated article reached. The quantity was reduced automatically.'); // Now we use MAX_PROD_QTY_EXCEEDED

define('ERROR_REVIEW_TEXT', 'The text must consist of at least ' . REVIEW_TEXT_MIN_LENGTH . ' characters.');
define('ERROR_REVIEW_RATING', 'Enter your review.');
define('ERROR_REVIEW_AUTHOR', 'Enter your name.');
define('ERROR_REVIEW_PRODUCT_PURCHASED', 'Only products that have also been purchased from us can be reviewed.');

define('GV_NO_PAYMENT_INFO', '<div class="infomessage">You can pay the order with your credit completely. If you do not want to redeem your balance, clear the credit selection and select a payment method!</div>');
define('GV_ADD_PAYMENT_INFO', '<div class="errormessage">Your credit is not sufficient or may not be used for all accounting-positions to pay the order completely. Please select a payment method in addition!</div>');

define('_SHIPPING_FREE','Free Shipping');
define('TEXT_INFO_FREE_SHIPPING_COUPON', 'The shipping costs are covered by your coupon.');

define('TEXT_CONTENT_NOT_FOUND', 'Page not found');
define('TEXT_SITE_NOT_FOUND', 'Page not found');

// error message for exceeded product quantity, noRiddle
define('MAX_PROD_QTY_EXCEEDED', 'The maximum allowed number of ' .MAX_PRODUCTS_QTY. ' for <span style="font-style:italic;">"%s"</span> has been exceeded. The number was automatically reduced to the permitted quantity.');

define('IMAGE_BUTTON_CONTENT_EDIT', 'Edit content');
define('PRINTVIEW_INFO_TITLE', 'Print datasheet');
define('PRINTVIEW_INFO_TEXT', 'Print datasheet');
define('PRODUCTS_REVIEW_LINK_TITLE', 'Write review');
define('PRODUCTS_REVIEW_LINK_TEXT', 'Write review');

define('TAX_INFO_SMALL_BUSINESS', 'Finalprice &sect; 19 UStG.');
define('TAX_INFO_SMALL_BUSINESS_FOOTER', 'Due to the small business status according to &sect; 19 UStG., we charge no sales tax');

define('NEED_CHANGE_PWD', 'Please change your Password.');
define('TEXT_REQUEST_NOT_VALID', 'This Link is not valid. Please make a new Password request.');

define('NAVBAR_TITLE_WISHLIST', 'Wishlist');
define('TEXT_TO_WISHLIST', 'Add to wishlist');
define('IMAGE_BUTTON_TO_WISHLIST', 'Add to wishlist');

define('GUEST_REDEEM_NOT_ALLOWED', 'Guests can not redeem any vouchers.');
define('GUEST_VOUCHER_NOT_ALLOWED', 'Vouchers can not be purchased as a guest.');

define('TEXT_FILTER_SETTING_DEFAULT', 'Items per page');
define('TEXT_FILTER_SETTING', '%s items per page');
define('TEXT_FILTER_SETTING_ALL', 'Show all items');
define('TEXT_SHOW_ALL', ' (show all)');
define('TEXT_FILTER_SORTING_DEFAULT', 'Sort by ...');
define('TEXT_FILTER_SORTING_ABC_ASC', 'A to Z');
define('TEXT_FILTER_SORTING_ABC_DESC', 'Z to A');
define('TEXT_FILTER_SORTING_PRICE_ASC', 'Price in ascending order');
define('TEXT_FILTER_SORTING_PRICE_DESC', 'Price in descending order');
define('TEXT_FILTER_SORTING_DATE_DESC', 'Newest products first');
define('TEXT_FILTER_SORTING_DATE_ASC', 'Oldest products first');
define('TEXT_FILTER_SORTING_ORDER_DESC', 'Most selling products');

define('NAVBAR_TITLE_ACCOUNT_CHECKOUT_EXPRESS_EDIT', 'Settings for my quick purchase');
define('SUCCESS_CHECKOUT_EXPRESS_UPDATED', 'The settings for My Quick purchase has been saved .');
define('TEXT_ERROR_CHECKOUT_EXPRESS_SHIPPING_ADDRESS', 'Please select a shipping address');
define('TEXT_ERROR_CHECKOUT_EXPRESS_SHIPPING_MODULE', 'Please select a shipping method');
define('TEXT_ERROR_CHECKOUT_EXPRESS_PAYMENT_ADDRESS', 'Please select a billing address');
define('TEXT_ERROR_CHECKOUT_EXPRESS_PAYMENT_MODULE', 'Please select a payment method');
define('TEXT_CHECKOUT_EXPRESS_INFO_LINK', 'My quick purchase');
define('TEXT_CHECKOUT_EXPRESS_INFO_LINK_MORE', 'More Informationen for my quick purchase &raquo;');
define('TEXT_CHECKOUT_EXPRESS_CHECK_CHEAPEST', 'Select always the cheapest shipping method');

define('AC_SHOW_PAGE', 'Page ');
define('AC_SHOW_PAGE_OF', ' from ');

define('FREE_SHIPPING_INFO', 'Free Shipping minimum order: %s');

define('TEXT_MANUFACTURER_NOT_FOUND', 'Manufacturer not found');
define('TEXT_PRODUCTS_NEW_NOT_FOUND', 'No new products in the last %s days found');
define('TEXT_SPECIALS_NOT_FOUND', 'No specials found');
define('TEXT_SEARCH_NOT_FOUND', 'No search results found');
define('ENTRY_TOKEN_ERROR', 'Please check your data.');

define('IMAGE_BUTTON_CONFIRM', 'Confirm'); // Needed for PayPal

// ***************************************************
//  Kontodaten-Prüfung
// ***************************************************
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_0', 'Bank details okay.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1', 'Account number and/or bank code are invalid or do not match!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2', 'The account number is not automatically testable.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_3', 'The account number is not testable.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_4', 'Account number is not testable! Please check your data again.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_5', 'This routing number does not exist, please correct your entry.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_8', 'Error in the bank code or no bank code specified!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_9', 'No account number specified!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_10', 'You do not have account holders indicated.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_128', 'Internal error when checking the bank details.');

// Fehlermeldungen alle IBAN-Nummern 
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1000', 'In IBAN included country code (1st and 2nd place) unknown.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1010', 'IBAN length wrong: Too many points entered.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1020', 'IBAN length wrong: Too few points entered.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1030', 'IBAN is not equivalent to that established for the country format.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1040', 'Check digits of IBAN (points 3 and 4) not correctly -> Typo in the IBAN.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1050', 'BIC has invalid format.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1060', 'BIC-length wrong: Too many characters entered. 8 or 11 characters are required.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1070', 'BIC-length wrong: Zu wenige Zeichen angeben. 8 or 11 characters are required.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1080', 'BIC-length invalid: 8 or 11 characters are required.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_1200', 'IBANs from the specified country (1st and 2nd place of the IBAN) are not accepted.');

// Fehlermeldungen für deutsche Kontonummern 
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2001', 'In IBAN included account number (points 13 to 22) and/or routing number (points 5 to 12) invalid or do not match each other.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2002', 'In IBAN included account number (points 13 to 22) is not automatically testable.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2003', 'For in IBAN included account number (points 13 to 22) there is no check digit defined.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2004', 'In IBAN included account number (points 13 to 22) is not testable!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2005', 'Bank code (points 5 to 12 of the IBAN) nonexistent!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2008', 'Error in the bank code (points 5 to 12 of the IBAN) or no bank code specified!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2009', 'No account number (points 13 to 22 of the IBAN) specified!');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2010', 'No account holders indicated.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2020', 'BIC invalid: No bank existent with this BIC.');
define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_2128', 'Internal error when checking the bank details.');

define('BANKACCOUNT_CHECK_TEXT_BANK_ERROR_UNKNOWN', 'Unknown error when checking the bank details.');

define('PRODUCT_REVIEWS_SUCCESS', 'Thank you for your review.');
define('PRODUCT_REVIEWS_SUCCESS_WAITING', 'Thank you for your review. This will be checked before it is published.');

define('TITLE_PRODUCTS_NEW', 'New products');
define('TITLE_SPECIALS', 'Special offers');

define('TEXT_SHOW_CATEGORY', 'Show all from ');
define('TEXT_MENU_TITLE', 'Categories');

define('NAVBAR_TITLE_ERROR', 'Error');

define('TEXT_ERROR_HANDLER_400', 'Error 400: Bad Request.');
define('TEXT_ERROR_HANDLER_401', 'Error 401: Unauthorized.');
define('TEXT_ERROR_HANDLER_403', 'Error 403: Forbidden.');
define('TEXT_ERROR_HANDLER_404', 'Error 404: Not Found!');
define('TEXT_ERROR_HANDLER_500', 'Error 500: Internal Server Error.');

define('ERROR_PRODUCTS_MISSING_CONFIGURATION_NOT_ADDED', 'Some items from your order are no longer available in the desired configuration and have not been placed in the shopping cart.');

define('TEXT_MAX_ADDRESS_NOTE', 'Attention, there is no new address available. You have reached the maximum number of %s addresses. This address will also be used as the shipping address. If you need a different billing address, select an address from your address book.');

define('TEXT_FILTER_SET_LABEL', 'Items per page');
define('TEXT_FILTER_SORT_LABEL', 'Sorting');
define('TEXT_FILTER_MANUFACTURERS_LABEL', 'Manufacturers');
define('TEXT_COUNTRY_LABEL', 'Country');
define('TEXT_STATE_LABEL', 'State');

define('TEXT_IMG_VVCODE', 'Security Code');
