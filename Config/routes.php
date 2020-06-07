<?php
	
$router->addVarriableRoute("post", "/user/register", ["controller"=>"UserController", "method"=>"register"]);
$router->addVarriableRoute("post", "/user/login", ["controller"=>"UserController", "method"=>"login"]);
$router->addVarriableRoute("get", "/user/logout", ["controller"=>"UserController", "method"=>"logout"]);
$router->addVarriableRoute("get", "/user/profile", ["controller"=>"UserController", "method"=>"getProfile"]);
$router->addVarriableRoute("get", "/user/profile/{id}", ["controller"=>"UserController", "method"=>"getProfile"]);