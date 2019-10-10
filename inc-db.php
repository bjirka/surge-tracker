<?php
$_CONFIG["db_host"] = "localhost";
$_CONFIG["db_username"] = "";
$_CONFIG["db_password"] = "";
$_CONFIG["db_database"] = "redd38_trackmysurge";

$dbLink = mysql_connect ($_CONFIG["db_host"], $_CONFIG["db_username"], $_CONFIG["db_password"]);
mysql_select_db($_CONFIG["db_database"], $dbLink);

date_default_timezone_set("America/Chicago");