<?php


use core\Engine;
use core\exception\BadResultException;

class RemoteBase{
    protected static function src($path, $method = "GET", $trace = false){
        $host = Engine::getInstance()->prop("engine.host");

	    $contextData  = [
		    "http" => [
			    "method" => $method,
			    "header" => "skey:".Engine::getInstance()->prop("engine.skey")
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