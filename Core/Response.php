<?php

namespace Core;

Class Response {

	private $content=null;
	private $code=200;
	private $headers = [
            'Access-Control-Allow-Origin' => "",
            'Access-Control-Allow-Methods'=> 'GET, POST, DELETE, PUT, OPTIONS, HEAD, PATCH',
            'Access-Control-Allow-Headers'=> 'Authorization,DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Set-Cookie',
            'Access-Control-Allow-Credentials'=> 'true'
    ];

	public function __construct($content=null){
		$this->content=$content;
		$this->headers['Access-Control-Allow-Origin'] = env("HOST_URL");
	}

	public function setContent($content){
		$this->content=$content;
		return $this;
	}

	public function setHttpCode($code){
		$this->code=$code;
		return $this;
	}

	public function sendHeaders(){
		http_response_code($this->code);
		foreach ($this->headers as $header => $value) {
			header($header.":".$value);
		}
	}

	public function setHeader($header, $value){
		$this->headers[$header] =$value;
		return $this;
	}

	public function sendJson($content){
		$this->setHeader("Content-Type", "application/json");
		$this->send(json_encode($content));
	}

	public function send($content){
		session_write_close();
		if(!is_null($content)){
			$this->content=$content;
		}
		$this->sendHeaders();
		echo $this->content;
		exit();
	}
}