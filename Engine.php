<?php

namespace core;

use ContextDto;
use core\exception\CoreErrorException;
use core\exception\SqlException;
use core\utils\ExceptionUtils;
use Exception;

class Engine{
	protected static $instance = null;
	private static $urlDirs = null;
	private $root;
	private $host;
	private $locale;
	private $settings;

	public function __construct(){
		try{
			self::$urlDirs  = self::initDirs();
			$this->settings = parse_ini_file(__DIR__ . '/app.ini');
			$this->root     = $this->prop("engine.template");
		}catch(Exception $se){
			echo ExceptionUtils::printException($se);
		}
	}

	public function sessionId(){
		ContextDto::getInstance()->sess();
	}

	public function builder(){
		return $this;
	}

	public function host($host){
		$this->host = $host;

		return $this;
	}

	public function getLocale(){
		if($_COOKIE["locale"] ?? null){
			return $_COOKIE["locale"];
		}

		return $this->prop("engine.locale");
	}

	public function setLocale($locale = null){

		$expire = 3600 * 24 * 365;
		setcookie("locale", $locale, time() + $expire, "/");

		return $locale;
	}

	public function templateRoot($root){
		$this->root = $root;

		return $this;
	}

	public static function getInstance(){
		self::$instance;
		if(is_null(self::$instance)){
			self::$instance = new Engine();
		}

		return self::$instance;
	}

	public function prop($name){
		if(isset($this->settings[ $name ])){
			return $this->settings[ $name ];
		}

		return null;
	}

	public static function getDir($i){
		if(!self::$urlDirs){
			self::$urlDirs = self::initDirs();
		}
		$dirs = self::$urlDirs;
		if(sizeof($dirs) > $i){
			return $dirs[ $i ];
		}

		return null;
	}

	public function getDirsLegacy($from = 0, $to = 0){
		$dirs = self::$urlDirs;
		$res  = $dirs;
		if($to > 0){
			$to += $from;
		}
		if($from > sizeof($dirs) && $to < 1){
			return array();
		}
		if($from > 0 || $to > 0){
			$res = array();
			foreach($dirs as $i => $dir){
				if($to > 0 && $i >= $to){
					break;
				}
				if($i >= $from){
					$res[] = $dir;
				}
			}
		}

		return $res;
	}

	public function getTemplateFile(){
		$dirs = array();
		foreach(self::$urlDirs as $dir){
			$dirs[] = $dir;
		}
		$templateRoot = __DIR__ . "/../" . $this->root;
		$target       = null;
		while(!empty($dirs) && !$target){
			$checkDir = implode("/", $dirs);
			if(file_exists($templateRoot . $checkDir . ".php")){
				$target = $checkDir . ".php";
			}
			if(!$target && file_exists($templateRoot . $checkDir . "/index.php")){
				$target = $checkDir . "/index.php";
			}
			array_pop($dirs);
		}

		if(file_exists($templateRoot . $target) && is_file($templateRoot . $target)){
			return $templateRoot . $target;
		}
		return null;
	}

	//---------------------------------------
	public function runProject(){
		$path = $this->getTemplateFile();
		try{
			if(!$path){
				http_response_code(404);
				if(file_exists(__DIR__ . "/" . $this->root . "errors/404.php")){
					require_once __DIR__ . "/" . $this->root . "errors/404.php";
				}
				exit();
			}else{
				require_once($path);
			}
		}catch(SqlException | CoreErrorException $se){
			require_once(__DIR__ . "/../" . $this->root . "errors/500.php");
			error_log(nl2br(ExceptionUtils::printException($se)));
		}catch(Exception $e){
			require_once(__DIR__ . "/../" . $this->root . "errors/500.php");

			return false;
			//echo $e->getFile() . " #" . $e->getLine() . "<br/>";
		}

		return true;
	}

	private static function initDirs(){
		$tmp = trim($_SERVER["REQUEST_URI"], "/");
		$tmp = preg_replace("#(\?.+$)#mi", "", $tmp);
		$tmp = trim($tmp, "/");
		$tmp = trim($tmp, "?");

		return preg_split("#\/#mi", $tmp);
	}

	public function getDirs($from = 0){
		return array_slice(self::$urlDirs, $from);
	}
}
