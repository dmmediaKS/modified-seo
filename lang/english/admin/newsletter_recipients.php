<?php
/* -----------------------------------------------------------------------------------------
   $Id: newsletter_recipients.php 15799 2024-04-10 13:58:13Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  define('HEADING_TITLE', 'Newsletter recipient');

  define('TABLE_HEADING_NEWSLETTER', 'E-Mail Address');
  define('TABLE_HEADING_FIRSTNAME', 'Firstname');
  define('TABLE_HEADING_LASTNAME', 'Lastname');
  define('TABLE_HEADING_CUSTOMERS_STATUS', 'Customers status');
  define('TABLE_HEADING_STATUS', 'Status');
  define('TABLE_HEADING_ACTION', 'Action');
  define('TABLE_HEADING_DATE_ADDED', 'Added');

  define('ENTRY_MAIL_STATUS', 'Status:');
  define('ENTRY_SEARCH_CUSTOMER', 'Search:');
  
  define('TXT_SUBSCRIBED', 'subscribed');
  define('TXT_UNSUBSCRIBED', 'unsubscribed');
  define('TXT_UNCONFIRMED', 'unconfirmed');

  define('TEXT_INFO_HISTORY_NEWSLETTER', 'History:');
  define('TEXT_INFO_HISTORY_NEWSLETTER_NONE', 'no history available');
  define('TEXT_INFO_HEADING_DELETE_NEWSLETTER', 'E-Mail Address unsubscribe');
  define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to unsubscribe this e-mail address?');

  define('BUTTON_UNSUBSCRIBE', 'E-Mail unsubscribe');
  define('BUTTON_REMIND', 'E-Mail opt-in');

  define('TEXT_EMAIL_SUBJECT','Your newsletter subscription');
  define('TEXT_EMAIL_ACTIVE','This e-mail address has successfully been registered for the newsletter!');
  define('TEXT_EMAIL_ACTIVE_ERROR','An error occured, this e-mail address has not been registered for the newsletter!');
  define('TEXT_EMAIL_DEL','E-Mail address was deleted successfully from newsletter database.');
  define('TEXT_EMAIL_DEL_ERROR','An Error occured, E-Mail address has not been removed from database!');
  define('TEXT_EMAIL_EXIST_NO_NEWSLETTER','This e-mail address is registered but not yet activated!');
  define('TEXT_EMAIL_EXIST_NEWSLETTER','This e-mail address is already registered for the newsletter!');
  define('TEXT_EMAIL_NOT_EXIST','This e-mail address is not registered for newsletters!');
  define('TEXT_EMAIL_INPUT', 'Opt-in E-Mail sent.');
