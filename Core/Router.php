<?php

namespace Core;

use Core\Request;

Class Router {

	const CONTROLLER_NAMESPACE="App\Controller\\";
	private $routes =[];
	private $matchedRoute=[];
	private $pathInput=[];

	public function __construct($request, $response){
		$this->request = $request;
		$this->response = $response;
		$this->loadRoutes($this);
	}

	public function loadRoutes($router){
		include("../Config/routes.php");
	}

	public function addRoute($method, $path, $action){
		$this->routes[$path]=['method'=>$method, "action"=>$action];
	}

	public function addVarriableRoute($method, $path, $action){
		$parts =explode("/", $path);
		$newvalues= [];
		$varriable=[];
		foreach ($parts as $key => $value) {
			$matches =[];
			preg_match("/^{([a-zA-z)]+)\}$/", $value, $matches);
			if(count($matches)>0){
				$var = $matches[1];
				array_push($varriable, $var);
				$value = preg_replace("/^{([a-zA-z)]+)\}$/", "(?<$var>[a-zA-z0-9)]+)", $value);
			}else{
				$value= preg_quote($value);
			}
			array_push($newvalues, $value);
		}
		$newpath= implode("\/", $newvalues);

		$this->routes[$newpath]=['method'=>$method, "action"=>$action, "varriables"=>$varriable];

	}

	public function matchRoute(){
		$path  =  $this->request->getRequestPath();
		$method = $this->request->method();
		$found = isset($this->routes[$path]) && 
					strtoupper($this->routes[$path]['method']) == $method ? true : false;
		if($found){
			$this->matchedRoute = $this->routes[$path];
		}
		return $found;
	}

	public function matchVarriableRoutes(){
		$found = false;
		$path  =  $this->request->getRequestPath();
		$method = $this->request->method();

		foreach ($this->routes as $key => $value) {
			$matches=[];
			$pattern = "/^".$key."$/";
			preg_match($pattern, $path, $matches);
			if(count($matches)>0 && strtoupper($value['method']) == $method){
				$this->matchedRoute = $value;
				foreach ($value['varriables'] as $value) {
					$this->pathInput[$value] = $matches[$value];
				}
				$found=true;
				break;
			}

		}
		return $found;
	}

	public function dispatch(){
		$this->matchVarriableRoutes();
		if(empty($this->matchedRoute)){
			throw new \Exception("There is no matched route");
		}

		$controller = Router::CONTROLLER_NAMESPACE.$this->matchedRoute['action']['controller'];
		$action = $this->matchedRoute['action']['method'];

		if(!class_exists($controller)){
			throw new \Exception("No controller exists with specfied name ".$controller);
		}

		$instance = new $controller($this->request, $this->response);

		if(!is_callable([$instance, $action])){
			throw new \Exception("No action exists with specfied name".$action. " in controller ".$controller);
		}
		$instance->$action($this->request, $this->response, ...array_values($this->pathInput));

	}
}