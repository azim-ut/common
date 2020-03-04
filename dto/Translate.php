<?php


class Translate extends RemoteBase{
    protected static $instance = null;
    private static $cache = [];
    private static $lang = "en";

    /**
     * @return Translate
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
            $obj            = self::src(HOST . "core/rest/translate/translates");
            self::$cache    = $obj->list;
            self::$lang     = preg_split("#_#", $obj->lang)[0];
        }

        return self::$instance;
    }

    public function get($code){
        return self::$cache->$code ?? $code;
    }

    public function lang(){
        return self::$lang;
    }
}