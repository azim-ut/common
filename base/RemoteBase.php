<?php


use core\Engine;
use core\exception\BadResultException;

class RemoteBase{
    protected static function getData($path, $trace = false){
        $host = Engine::getInstance()->prop("engine.host");

        $path = $host . $path;
        $res  = self::curlGet($path);
        if($trace){
            var_dump($path, $res);
        }
        $obj = json_decode($res);

        return $obj->data ?? null;
    }

    protected static function postData($path, $params, $trace = false){
        $host = Engine::getInstance()->prop("engine.host");

        $path = $host . $path;
        $res  = self::curlPost($path, $params);
        if($trace){
            var_dump($path, $res);
        }
        $obj = json_decode($res);
        if($obj->sess ?? null){
            session_create_id($obj->sess);
        }

        return $obj->data ?? null;
    }

    private static function getLocalSessionId(){
        if(!($_COOKIE["PHPSESSID"]??null)){
            $_COOKIE["PHPSESSID"] = session_id();
        }
        return $_COOKIE["PHPSESSID"];
    }

    private static function curlPost($to, $params, $headers = array("Content-Type: application/x-www-form-urlencoded")){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_HEADER         => 0,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE        => true,
            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_URL            => $to,
            CURLOPT_HTTPHEADER     => [
                "skey: " . Engine::getInstance()->prop("engine.skey"),
                "sid: " . self::getLocalSessionId(),
	            'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"
            ],
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            throw new BadResultException('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        curl_close($curl);

        return $resp;
    }

    private static function curlGet($to, $headers = array("Content-Type: application/x-www-form-urlencoded")){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE        => true,
            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_URL            => $to,
            CURLOPT_HTTPHEADER     => [
                "skey: " . Engine::getInstance()->prop("engine.skey"),
                "sid: " . self::getLocalSessionId(),
	            'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"
            ],
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            throw new BadResultException('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        curl_close($curl);

        return $resp;
    }
}