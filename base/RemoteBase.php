<?php


use core\exception\BadResultException;

class RemoteBase{
    protected static function src($path, $method = "GET"){
        $context = stream_context_create([
            "http" => [
                "method" => $method,
                "header" => "skey:".SKEY
            ]
        ]);
        $res = file_get_contents($path, false, $context);
        $obj = json_decode($res);

        return $obj->data ?? null;
    }
}