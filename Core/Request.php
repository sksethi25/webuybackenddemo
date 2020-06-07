<?php

namespace Core;

use Core\Validator;
use Core\Base\ValidatorBase;

Class Request {

	private $methodData =null;
	private $getData=null;
	private $postData=null;
	private $bodyData =null;
	private $validator = null;

	public function __construct(){
		$this->setValidator(new Validator($this));
		$this->startSession();
	}

	public function setValidator(ValidatorBase $validator){
		$this->validator = $validator;
	}


	public function capture(){
		$this->methodData = filter_input(\INPUT_SERVER, 'REQUEST_METHOD', \FILTER_SANITIZE_SPECIAL_CHARS );
		$this->getData=  filter_input_array (\INPUT_GET);
		$this->postData=  filter_input_array (\INPUT_POST);
		$this->bodyData = file_get_contents('php://input');

		$this->requestUri = filter_input(\INPUT_SERVER, 'REQUEST_URI', \FILTER_SANITIZE_SPECIAL_CHARS );
		$this->requestUriParsed = parse_url($this->requestUri);
		$this->requestPath= $this->requestUriParsed['path'];
	}

	public function method(){
		if(!empty($this->methodData)){
			return $this->methodData;
		}
		return "GET";
	}

	public function get(){
		if(!empty($this->getData)){
			return $this->getData;
		}
		return [];
	}
	public function getKey($key){
		return $this->getData[$key] ?? null;
	}

	public function getKeyExists($key){

		return isset($this->getData[$key]) ?? false;
	}

	public function post(){
		if(!empty($this->postData)){
			return $this->postData;
		}
		return [];
	}

	public function postKey($key){
		return $this->postData[$key] ?? null;
	}

	public function postKeyExists($key){
		return isset($this->postData[$key]) ?? false;
	}

	public function postOnly($params=[]){
		if(!empty($this->postData)){
			return array_intersect_key($this->postData, 
                    array_flip($params));
		}
		return [];
	}

	public function inputExists($param){
		if($this->method() =="GET"){
			return $this->getKeyExists($param);
		}else{
			return $this->postKeyExists($param);
		}
	}

	public function input($param){
		if($this->method() =="GET"){
			return $this->getKey($param);
		}else{
			return $this->postKey($param);
		}
	}

	public function body(){
		if(!empty($this->bodyData)){
			return $this->bodyData;
		}
		return [];
	}

	public function getRequestPath(){
		return $this->requestPath;
	}

	public function setSession($key, $value){
		$_SESSION[$key] = $value;
	}

	public function getSession($key){
		return $_SESSION[$key] ?? null;
	}

	public function startSession(){
		session_save_path ("/tmp/") ;
		session_set_cookie_params (3600, "/", "", false, false);
		session_start();
	}

	public function destroySession(){
		session_unset();
		session_destroy();
	}

	public function validator(){
		return $this->validator;
	}
}