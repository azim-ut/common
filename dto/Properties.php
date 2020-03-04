<?php


class Properties extends RemoteBase{
    protected static $instance = null;
    private $facebookID = null;

    /**
     * @return Properties
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getFacebookAuthAppID(){
        if(!$this->facebookID){
            $this->facebookID = self::src(HOST . "core/rest/property/fb/app/id");
        }
        return $this->facebookID;
    }
}