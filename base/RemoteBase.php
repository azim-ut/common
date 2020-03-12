<?php


use core\Engine;
use core\exception\BadResultException;

class RemoteBase{
    protected static function src($path, $method = "GET", $trace = false){
        $host = Engine::getInstance()->prop("engine.host");

	    $contextData  = [
		    "http" => [
			    "method" => $method,
			    "header" => 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n".
                            "skey:".Engine::getInstance()->prop("engine.skey")."\r\n".
                            "User-Agent:".$_SERVER['HTTP_USER_AGENT']."\r\n"
		    ]
	    ];
	    $context = stream_context_create($contextData);
        $path = $host.$path;
	    $res = file_get_contents($path, false, $context);
	    if($trace){
		    var_dump($path, $contextData, $res);
	    }
	    $obj = json_decode($res);

	    return $obj->data ?? null;
    }
}