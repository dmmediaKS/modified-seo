<?php
/* -----------------------------------------------------------------------------------------
   $Id: newsletter_recipients.php 15799 2024-04-10 13:58:13Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  define('HEADING_TITLE', 'Newsletter Empf&auml;nger');

  define('TABLE_HEADING_NEWSLETTER', 'E-Mail Adresse');
  define('TABLE_HEADING_FIRSTNAME', 'Vorname');
  define('TABLE_HEADING_LASTNAME', 'Nachname');
  define('TABLE_HEADING_CUSTOMERS_STATUS', 'Kundengruppe');
  define('TABLE_HEADING_STATUS', 'Status');
  define('TABLE_HEADING_ACTION', 'Aktion');
  define('TABLE_HEADING_DATE_ADDED', 'Hinzugef&uuml;gt');
  
  define('ENTRY_MAIL_STATUS', 'Status:');
  define('ENTRY_SEARCH_CUSTOMER', 'Suche:');

  define('TXT_SUBSCRIBED', 'abonniert');
  define('TXT_UNSUBSCRIBED', 'abgemeldet');
  define('TXT_UNCONFIRMED', 'unbest&auml;tigt');

  define('TEXT_INFO_HISTORY_NEWSLETTER', 'Historie:');
  define('TEXT_INFO_HISTORY_NEWSLETTER_NONE', 'keine Historie verf&uuml;gbar');
  define('TEXT_INFO_HEADING_DELETE_NEWSLETTER', 'E-Mail Adresse abmelden');
  define('TEXT_INFO_DELETE_INTRO', 'Sind Sie sicher, dass Sie diese E-Mail Adresse vom Newsletter abmelden m&ouml;chten?');

  define('BUTTON_UNSUBSCRIBE', 'E-Mail abmelden');
  define('BUTTON_REMIND', 'E-Mail opt-in');

  define('TEXT_EMAIL_SUBJECT','Ihre Newsletter-Anmeldung');
  define('TEXT_EMAIL_ACTIVE','Die E-Mail-Adresse wurde erfolgreich f&uuml;r den Newsletterempfang freigeschaltet!');
  define('TEXT_EMAIL_ACTIVE_ERROR','Es ist ein Fehler aufgetreten, die E-Mail-Adresse wurde nicht freigeschaltet!');
  define('TEXT_EMAIL_DEL','Die E-Mail-Adresse wurde aus der Newsletterdatenbank gel&ouml;scht.');
  define('TEXT_EMAIL_DEL_ERROR','Es ist ein Fehler aufgetreten, die E-Mail-Adresse wurde nicht gel&ouml;scht!');
  define('TEXT_EMAIL_EXIST_NO_NEWSLETTER','Diese E-Mail-Adresse existiert bereits in unserer Datenbank, ist aber noch nicht f&uuml;r den Empfang des Newsletters freigeschaltet!');
  define('TEXT_EMAIL_EXIST_NEWSLETTER','Diese E-Mail-Adresse existiert bereits in unserer Datenbank und ist f&uuml;r den Newsletterempfang bereits freigeschaltet!');
  define('TEXT_EMAIL_NOT_EXIST','Diese E-Mail-Adresse existiert nicht in unserer Datenbank!');
  define('TEXT_EMAIL_INPUT', 'Die Opt-in E-Mail wurde erneut versendet.');
