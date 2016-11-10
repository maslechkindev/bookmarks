<?php

namespace Classes;

class Routing
{
    private $url;
    private $urlParams;
    private $action;
    private $userIp;
    private $_PUT;
    private $_DELETE;
    private $urlAttributes = [];
    private $methods = [
        'GET' => 'get',
        'POST' => 'set',
        'PUT' => 'update',
        'DELETE' => 'delete',
    ];
    private static $route = null;

    const URL_USER = 'user';
    const UID= 'uid';
    const PATH_2_CLASSES = "Classes\\";

    public function __construct()
    {
        $this->url = substr($_SERVER['REQUEST_URI'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1);
        if($this->url != ""){
            $urlPath = explode('?',$this->url);
            $this->urlParams = explode('/',$urlPath[0]);
            $this->urlAttributes = $this->setUrlAttribute($urlPath[1],
                !empty($this->urlParams[3]) ? $this->urlParams[3] : null);
            $this->action = $_SERVER['REQUEST_METHOD'];
            $this->userIp = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
            if(!$this->checkUser()){
                $this->return404Error();
            }
            $this->checkPath();
        } else {
            $this->returnEmptyPage();
        }

    }

    public static function getRoute()
    {
        return self::$route == null ? new self : self::$route;
    }

    public function getUrlParams()
    {
        return $this->urlParams;
    }

    public function getUrlAttributes()
    {
        return $this->urlAttributes;
    }

    public function getAction()
    {
        return $this->action;
    }

    private function checkUser()
    {
        if(!empty($this->urlParams) && !empty($this->urlParams[0]) && !empty($this->urlParams[1])){
            if($this->urlParams[0] == self::URL_USER && $this->urlParams[1] == $this->userIp){
                return true;
            }
        }
        return false;
    }

    private function checkPath()
    {
        if(!empty($this->urlParams) && !empty($this->urlParams[2]) && !empty($this->getAction())){
                $className = $this->getClass($this->urlParams[2]);
                if($className != null){
                    $methodName = $this->getMethod($className);
                    $class = self::PATH_2_CLASSES.$className;
                    $obj = new $class();
                    $attributes = $this->getUrlAttributes();
                    $obj->$methodName($attributes);
                }
        }
        return false;
    }

    private function getClass($class)
    {
        require_once (__DIR__.'/'.ucfirst($class).'.php');
        return class_exists(self::PATH_2_CLASSES.ucfirst($class)) ? ucfirst($class) : null;
    }

    private function getMethod($class)
    {
        $method = !empty($this->methods[$this->getAction()]) ? $this->methods[$this->getAction()].$class : null;
        return $method != null && method_exists(self::PATH_2_CLASSES.$class, $method) ? $method : null;
    }

    private function return404Error()
    {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    private function returnEmptyPage()
    {
        return true;
    }

    private function setUrlAttribute($urlPath, $uid){
        $attr = [];
        $urlAttributes = strpos($urlPath, '&') ? explode('&',$urlPath) : [];
        foreach($urlAttributes as $urlAttribute){
            $attribute = explode('=',$urlAttribute);
            if($attribute[0] != '' && $attribute[0] != null && $attribute[1] != '' && $attribute[1] != null){
                $attr[$attribute[0]] = $attribute[1];
            }
        }
        if(!empty($uid) && $uid != null){
            $attr[self::UID] = $uid;
        }
        foreach($this->getPutAttributes() as $k=>$v){
            $attr[$k] = $v;
        }
        foreach($this->getDeleteAttributes() as $k=>$v){
            $attr[$k] = $v;
        }
        if(!empty($_POST)) {
            foreach ($_POST as $k => $v) {
                $attr[$k] = $v;
            }
        }
        if(!empty($_GET)) {
            foreach ($_GET as $k => $v) {
                $attr[$k] = $v;
            }
        }
        return $attr;
    }

    private function getPutAttributes(){
        $_PUT = array();
        if($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $putdata = file_get_contents('php://input');
            $exploded = explode('&', $putdata);

            foreach($exploded as $pair) {
                $item = explode('=', $pair);
                if(count($item) == 2) {
                    $vals = explode('"', $item[1]);
                    $key = $vals[1];
                    $value = explode('-', urldecode($vals[2]));
                    $_PUT[$key] = trim($value[0]);
                }
            }
        }
        return $_PUT;
    }

    private function getDeleteAttributes(){
        $_DELETE = array();
        if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $data = file_get_contents('php://input');
            $exploded = explode('&', $data);
            foreach($exploded as $pair) {
                $item = explode('=', $pair);
                if(count($item) == 2) {
                    $vals = explode('"', $item[1]);
                    $key = $vals[1];
                    $value = explode('-', urldecode($vals[2]));
                    $_DELETE[$key] = trim($value[0]);
                }
            }
        }
        return $_DELETE;
    }
}