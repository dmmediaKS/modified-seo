# -----------------------------------------------------------------------------------------
#  $Id: update_3.1.0_to_3.1.1.sql 16173 2024-10-11 08:54:06Z Tomcraft $
#
#  modified eCommerce Shopsoftware
#  http://www.modified-shop.org
#
#  Copyright (c) 2009 - 2013 [www.modified-shop.org]
#  -----------------------------------------------------------------------------------------

#Tomcraft - 2024-10-10 - changed database_version
INSERT INTO `database_version` (`version`, `date_added`) VALUES ('MOD_3.1.1', NOW());


# Keep an empty line at the end of this file for the db_updater to work properly