<?php

namespace Core;

require("../Core/Autoloader.php");
require("../Core/EnvAutoloader.php");
use Core\Autoloader as Autoloader;

class App {
	private $request;
	private $response;

	public function __construct(){
		Autoloader::initialize();
		$this->request = new Request();
		$this->response = new Response();
	}

	public function run(){
		$this->request->capture();
		$router = new Router($this->request, $this->response);
		$router->dispatch();
	}
}