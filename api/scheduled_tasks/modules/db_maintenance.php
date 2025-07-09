<?php
/* -----------------------------------------------------------------------------------------
   $Id: db_maintenance.php 14964 2023-02-08 10:40:00Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  function cron_db_maintenance() {
    $tables_query = xtc_db_query("SHOW TABLES FROM ".DB_DATABASE);
    while ($tables = xtc_db_fetch_array($tables_query)) {
      xtc_db_query("ANALYZE TABLE `".$tables['Tables_in_'.DB_DATABASE]."`");
      xtc_db_query("OPTIMIZE TABLE `".$tables['Tables_in_'.DB_DATABASE]."`");
    }
        
    return true;
  }