<?php

namespace App\Service;

Class ResponseService{
	
	private $response=null;

	public function __construct($response){
		$this->response = $response;
	}

	public function success(string $message, array $data=[], int $status=200)
    {
        $content = [
            'status' => true,
            'message'=> $message,
            'data'   => $data,
            'error'  => [],
        ];
       return $this->response->setHttpCode($status)->sendJson($content);
    }

    public function successMessage(string $message="")
    {
    	$this->success(!empty($message) ? $message :"Action Success", [], 200);
    }

	public  function error(string $message, string $error_type="", array $errors=[], int $status=404)
    {
        $content = [
            'status' => false,
            'message'=> $message,
            'data'   => new \stdClass,
            'error'  => ['code'=>$error_type, 'errors'=>$errors]
        ];

         return $this->response->setHttpCode($status)->sendJson($content);

    }

    public function errorMessage(string $message="")
    {
    	$this->error(!empty($message) ? $message :"Action failed", "", [], 400);
    }

    public function notFoundError(string $message="", int $status=404){
    	$this->error(!empty($message) ? $message :"Action Not Found", "", [], $status);
    }

	public  function validationFailed(array $erros, string $message="", int $status=400)
    {
        $errors = [];
        foreach ($erros as $key => $value) {
            $errors[] = [
                'key'     => $key,
                'message' => $value,
            ];
        }

        return $this->error(!empty($message) ? $message :"Validation Failed", "validation_failed",  $error, $status);

    }

    public  function serviceUnavailableError(string $message="", int $status=502)
    {
       return $this->error(!empty($message) ? $message :"Some Issue Happened,Please Try Again Laters", "", [], $status);

    }


}