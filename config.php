<?php
error_reporting(E_ALL);

ini_set("log_errors" , "1");
ini_set("error_log" , "errors.log");
date_default_timezone_set("Europe/Tallinn");

require_once(__DIR__ . "/Engine.php");
require_once(__DIR__ . "/base/RemoteBase.php");
define('HOST', "http://tukan/");
define('SKEY', "C801A8B9E8D4AA8E8CA3C14EF65F74C039A180F03ECEF26F19640DA1C675650B");