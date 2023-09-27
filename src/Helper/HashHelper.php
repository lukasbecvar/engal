<?php

namespace App\Helper;

/*
    Hash helper provides password hash & verify methods
*/

class HashHelper
{

	public function hash_validate($plain_text, $hash): bool {

		// default state
		$state = false;

		// check if password verified
		if (password_verify($plain_text, $hash)) {
			$state = true;
		} 

		return $state;
	}

	public function gen_bcrypt($plain_text, $cost): string {
		$options = [
			'cost' => $cost
		];
		$hash_final = password_hash($plain_text, PASSWORD_BCRYPT, $options);
		return $hash_final;
	}
}
