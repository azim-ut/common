<?php


class ContextDto extends RemoteBase{
    protected static $instance = null;
    private $sess = null;
    private $data = [];

    /**
     * @return ContextDto
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sess($name = null, $val = null){
        if(!$this->sess){
            $this->sess = self::getData("/core/rest/context/sess");
        }
        if($name && $val){
            $this->sess = self::postData("/core/rest/context/sess", [$name => $val]);

            if($this->sess->$name === $val){
                return true;
            }
            throw new Exception("SessionParamDefine exception",500);
        }
        if($name){
            return $this->sess->$name??null;
        }

        return $this->sess;
    }

    public function prop($name){
        if(!isset($this->data[ $name ])){
            $this->data[ $name ] = self::getData("/core/rest/context/prop/string/" . $name);
        }

        return $this->data[ $name ];
    }

    public function propArray($name){
        if(!isset($this->data[ $name ])){
            $this->data[ $name ] = self::getData("/core/rest/context/prop/array/" . $name);
        }

        return $this->data[ $name ];
    }
}