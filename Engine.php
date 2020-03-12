<?php

namespace core;

use AppDto;
use core\exception\CoreErrorException;
use core\exception\SqlException;
use core\utils\ExceptionUtils;
use Exception;
use RemoteBase;

class Engine{
    protected static $instance = null;
    private static $urlDirs = null;
    private $root;
    private $host;
    private $locale;
    private $settings;

    public function __construct(){
        try{
            self::$urlDirs = self::initDirs();
	        $this->settings = parse_ini_file(__DIR__ . '/app.ini');
        }catch(Exception $se){
            echo ExceptionUtils::printException($se);
        }
    }

    public function sessionId(){
        return AppDto::getInstance()->sess();
    }

    public function builder(){
        return $this;
    }

    public function host($host){
        $this->host = $host;
        return $this;
    }

    public function locale($locale){
        $this->locale = $locale;
        return $this;
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
		if(isset($this->settings[$name])){
			return $this->settings[$name];
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
        $templateFile = __DIR__ . "/../" . $this->root ?? "index.php";

        while(sizeof($dirs)){
            $checkDir = __DIR__ . "/../" . $this->root . implode("/", $dirs);
            if(file_exists($checkDir . "/index.php")){
                $templateFile = $checkDir . "/index.php";
                break;
            }else if(file_exists($checkDir . ".php")){
                $templateFile = $checkDir . ".php";
                break;
            }
            array_pop($dirs);
        }

        return $templateFile;
    }

    //---------------------------------------
    public function runProject(){
        $templateFile = $this->getTemplateFile();
        try{
            if(false === ($data = @file_get_contents($templateFile))){
                http_response_code(404);
                require_once($this->root . "errors/404.php");
            }else if(empty($templateFile) || !file_exists($templateFile) || is_dir($templateFile)){
                require_once($templateFile . "/index.php");
            }else{
                require_once($templateFile);
            }
        }catch(SqlException | CoreErrorException $se){
            require_once(__DIR__ . "/../" . $this->root . "error/500.html");
            error_log(nl2br(ExceptionUtils::printException($se)));
        }catch(Exception $e){
            require_once(__DIR__ . "/../" . $this->root . "error/500.html");

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
