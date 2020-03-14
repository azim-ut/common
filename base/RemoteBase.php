<?php


use core\Engine;
use core\exception\BadResultException;

class RemoteBase{
    protected static function src($path, $method = "GET", $trace = false){
        $host = Engine::getInstance()->prop("engine.host");

        $contextData = [
            "http" => [
                "method"  => $method,
                'timeout' => 3,
                "header"  => 'Cookie: ' . ($_SERVER['HTTP_COOKIE'] ?? '') . "\r\n" .
                             "skey:" . Engine::getInstance()->prop("engine.skey") . "\r\n" .
                             "User-Agent:" . $_SERVER['HTTP_USER_AGENT'] . "\r\n"
            ]
        ];

//	    $context = stream_context_create($contextData);
        $path = $host . $path;

//	    $res = file_get_contents($path, false, $context);
        $res = self::curlGet($path);
        if($trace){
            var_dump($path, $contextData, $res);
        }
        $obj = json_decode($res);

        return $obj->data ?? null;
    }


    private static function curlGet($to, $headers = array("Content-Type: application/x-www-form-urlencoded")){
        $curl = curl_init();

        $cookie = array();
        foreach( $_COOKIE as $key => $value ) {
            $cookie[] = "{$key}={$value}";
        };

        $cookie = implode('; ', $cookie);

        $curl_handle = curl_init();
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);


        $headers = self::getHeadersList();

//        curl_setopt($curl, CURLOPT_HTTPHEADER, "skey: ".Engine::getInstance()->prop("engine.skey"));
        $headers[] = "skey: ".Engine::getInstance()->prop("engine.skey");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE        => true,
            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_URL            => $to,
            CURLOPT_HTTPHEADER     => [
                "skey: ".Engine::getInstance()->prop("engine.skey")
            ],
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            throw new BadResultException('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        curl_close($curl);

        return $resp;
    }

    private static function getHeadersList(){
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}