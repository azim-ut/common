<?php
error_reporting(E_ALL);

ini_set("log_errors" , "1");
ini_set("error_log" , "errors.log");
date_default_timezone_set("Europe/Tallinn");

require_once(__DIR__ . "/Engine.php");
require_once(__DIR__ . "/base/RemoteBase.php");
require_once(__DIR__ . "/base/AppDto.php");