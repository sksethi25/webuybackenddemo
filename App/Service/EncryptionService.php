<?php

namespace App\Service;

Class EncryptionService {
	
	public function encrypt($input){
		if(is_array($input)){
			foreach ($input as $key => $value) {
				$input[$key] = $this->str_rot($value);
			}
		}else{
			$input = $this->str_rot($input);
		}
		return $input;
	}

	public function decrypt($input){
		if(is_array($input)){
			foreach ($input as $key => $value) {
				$input[$key] = $this->str_rot($value, 23);
			}
		}else{
			$input = $this->str_rot($input, 23);
		}
		return $input;
	}

	public function str_rot($string, $rot=3) {
    $letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
    // "% 26" allows numbers larger than 26
    // doubled letters = double rotated
    $dbl_rot = ((int) $rot % 26) * 2;
    if($dbl_rot == 0) { return $string; }
    // build shifted letter map ($dbl_rot to end + start to $dbl_rot)
    $map = substr($letters, $dbl_rot) . substr($letters, 0, $dbl_rot);
    // strtr does the substitutions between $letters and $map
    return strtr($string, $letters, $map);
  }
}