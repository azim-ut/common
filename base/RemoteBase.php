<?php


use core\Engine;
use core\exception\BadResultException;

class RemoteBase{
    protected static function src($path, $method = "GET", $trace = false){
        $host = Engine::getInstance()->prop("engine.host");

	    $contextData  = [
		    "http" => [
			    "method" => $method,
                'timeout' => 3,
			    "header" => 'Cookie: ' . ($_SERVER['HTTP_COOKIE']??'')."\r\n".
                            "skey:".Engine::getInstance()->prop("engine.skey")."\r\n".
                            "User-Agent:".$_SERVER['HTTP_USER_AGENT']."\r\n"
		    ]
	    ];

//	    $context = stream_context_create($contextData);
        $path = $host.$path;

//	    $res = file_get_contents($path, false, $context);
        $res = self::curlGet($path);
	    if($trace){
		    var_dump($path, $contextData, $res);
	    }
	    $obj = json_decode($res);

	    return $obj->data ?? null;
    }


    private static function curlGet($to, $headers=array("Content-Type: application/x-www-form-urlencoded")){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIE => "",
            CURLOPT_URL => $to
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            throw new BadResultException('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        curl_close($curl);
        return $resp;
    }
}