# -----------------------------------------------------------------------------------------
#  $Id: update_3.1.1_to_3.1.2.sql 16206 2024-11-04 16:23:34Z Tomcraft $
#
#  modified eCommerce Shopsoftware
#  http://www.modified-shop.org
#
#  Copyright (c) 2009 - 2013 [www.modified-shop.org]
#  -----------------------------------------------------------------------------------------

#Tomcraft - 2024-11-04 - changed database_version
INSERT INTO `database_version` (`version`, `date_added`) VALUES ('MOD_3.1.2', NOW());


# Keep an empty line at the end of this file for the db_updater to work properly