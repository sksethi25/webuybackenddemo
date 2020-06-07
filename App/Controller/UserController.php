<?php

namespace App\Controller;

use App\Model\User;
use App\Service\{EncryptionService, ResponseService};

class UserController {

	private const USER_REGISTER_RULES=[
			'email'=>'required|email|unique:User:email', 
			'password'=>['required'],
			'username'=>'required|unique:User:username',
			'contact'=>['required']

		];

	private const USER_LOGIN_RULES=[ 
			'password'=>['required'],
			'username'=>'required',
	];

	public function __construct($request, $response){
		$this->encryptionService= new EncryptionService();
		$this->responseService= new ResponseService($response);
		$this->validator = $request->validator();
	}
	
	public function register($request, $response){
		$errors = $this->validator->validate(UserController::USER_REGISTER_RULES,
			function($input){
			return $this->encryptionService->encrypt($input);
		});

		if(count($errors['error_messages'])>0){
			$this->responseService->error("Validation Failed,Please Check.", "validation_failed",$errors['error_messages']);
		}
		
		$input =$request->postOnly(array_keys(UserController::USER_REGISTER_RULES));
		$encrypted_input = $this->encryptionService->encrypt($input);

		$inserted= (new User())->create($encrypted_input);

		if($inserted == -1){
			$this->responseService->serviceUnavailableError();
		}
		
		$this->responseService->successMessage("User Created Successfully");
	}

	public function login($request){
		$errors = $this->validator->validate(UserController::USER_LOGIN_RULES,function($input){
			return $this->encryptionService->encrypt($input);
		});

		if(count($errors['error_messages'])>0){
			$this->responseService->error("Validation Failed", "validation_failed",$errors['error_messages']);
		}

		$input =$request->postOnly(array_keys(UserController::USER_LOGIN_RULES));
		$encrypted_input = $this->encryptionService->encrypt($input);

		$users = (new User())->findWhere($encrypted_input);

		if(count($users)==0){
			$this->responseService->notFoundError("Invalid User name or Password");
		}

		if(count($users)>1){
			$this->responseService->serviceUnavailableError();
		}

		$request->setSession('user_id', $users[0]['id']);
		$this->responseService->successMessage("User Loggedin Successfully");
	}

	public function logout($request){
		$request->destroySession();
		$this->responseService->successMessage("User Loggedout Successfully");
	}

	public function getProfile($request, $response, $id=null){
		$user_id = $request->getSession('user_id');

		if(is_null($user_id)){
			$request->destroySession();
			$this->responseService->error("Invalid User, login again", "logged_out", [], 401);
		}

		if(!is_null($id)){
			$user_id=$id;
		}

		$users = (new User())->find($user_id, ['email', 'username', 'contact']);

		if(count($users)!=1){
			$this->responseService->notFoundError("Profile not Found.");
		}

		$decrypted_user = $this->encryptionService->decrypt($users[0]);
		$this->responseService->success("profile found", ['user'=>$decrypted_user]);
	}
}