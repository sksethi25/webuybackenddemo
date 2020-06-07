<?php

namespace Core;

use Core\Base\ValidatorBase as ValidatorBase ;

Class Validator implements ValidatorBase {

	const MODEL_NAMESPACE="App\Model\\";
	
	public function __construct($request){
		$this->request =$request;
	}

	public function validate($ruleList, $input_mapper=null, $external_inputs=[]){
		$all_errors=['full_errors'=>[],'error_messages'=>[]];
	  	foreach ($ruleList as $key => $rules) {

	  		$input = isset($external_inputs[$key]) ? $external_inputs[$key] :  $this->request->input($key);
	  		$inputExists = isset($external_inputs[$key]) ? true : $this->request->inputExists($key);
	  		$input = is_callable($input_mapper) ? $input_mapper($input) : $input;
		  	if(is_string($rules)){
		  	 	$rules =explode("|", $rules);
		  	}

		  	$errors = [];
		  	$error_messages=[];
		  	foreach ($rules as $rule) {
		  		if($rule == "required"){
		  			if(!$inputExists){
		  				array_push($errors, ['rule'=>$rule, 'error'=>"It is a required field."]);
		  				array_push($error_messages, "It is a required field.");
		  				break;
		  			}
		  		}
		  		else if($inputExists && $rule == 'email'){
		  			$valid =filter_var($input, FILTER_VALIDATE_EMAIL);
		  			if(!$valid){
		  				array_push($errors, ['rule'=>$rule, 'error'=>"It should be a valid email."]);
		  				array_push($error_messages, "It should be a valid email.");
		  				break;
		  			}
		  		}else if($inputExists && strpos($rule, 'unique:') === 0){
		  			$unique_rule = explode(":", $rule);
		  			$count = count($unique_rule);
		  			if($count>1){
		  				$exact_rule = $unique_rule[0];
		  				$model =  $unique_rule[1];

		  				$column="id";
		  				if($count>2){
		  					$column = $unique_rule[2];
		  				}

		  				$rows =$this->queryModelInstance($model, [$column=>$input]);

		  				if($rows>0){
		  					array_push($errors, ['key'=>$key, 'rule'=>$rule, 'error'=>"It is a unique field,Given value already exists."]);
		  					array_push($error_messages, "It is a unique field,Given value already exists.");
		  					break;
		  				}
		  			}
	
		  		}else if($inputExists && strpos($rule, 'existsone:') === 0){
		  			$unique_rule = explode(":", $rule);
		  			$count = count($unique_rule);
		  			if($count>1){
		  				$exact_rule = $unique_rule[0];
		  				$model =  $unique_rule[1];

		  				$column="id";
		  				if($count>2){
		  					$column = $unique_rule[2];
		  				}

		  				$rows =$this->queryModelInstance($model, [$column=>$input]);

		  				if($rows!=1){
		  					array_push($errors, ['key'=>$key, 'rule'=>$rule, 'error'=>"It should uniquely exists"]);
		  					array_push($error_messages, "It should uniquely exists.");
		  					break;
		  				}
		  			}
	
		  		}
		  	}

		  	if(count($errors)>0){
				$all_errors['full_errors'][$key] =$errors;
				$all_errors['error_messages'][$key] =$error_messages;
		  	}
		  	
	  	}

	  	return $all_errors;
	}


	private function queryModelInstance($model, $where){
		$model = Validator::MODEL_NAMESPACE.$model;
		$action = "findCountWhere";

		if(!class_exists($model)){
			throw new \Exception("No model exists with specfied name ".$model." in unique rule");
		}

		$instance = new $model();

		if(!is_callable([$instance, $action])){
			throw new \Exception("No action exists with specfied name".$action. " in controller ".$model);
		}
		return $instance->$action($where);
	}
}