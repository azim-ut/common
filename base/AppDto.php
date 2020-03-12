<?php


class AppDto extends RemoteBase{
    protected static $instance = null;
    private $sess = null;
    private $data = [];

    /**
     * @return AppDto
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sess(){
        if($this->sess){
            $this->sess = self::src( "/core/rest/app/sess");
        }
        return $this->sess;
    }

    public function prop($name){
        if(!isset($this->data[$name])){
            $this->data[$name] = self::src( "/core/rest/property/prop/string/".$name);
        }
        return $this->data[$name];
    }

    public function propArray($name){
        if(!isset($this->data[$name])){
            $this->data[$name] = self::src( "/core/rest/property/prop/array/".$name);
        }
        return $this->data[$name];
    }
}