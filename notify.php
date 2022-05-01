<?php
	header('Content-Type:application/json; charset=utf-8');
	$content = file_get_contents('php://input');
	$api = 'http://ip:port/service-openup/toTradeWebPay';
	$appkey = 'a8955b02b5df475882038616d5448d43';
	$publicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgIwN9mVEWG9kagbxt2ippr8RNzK/fhBXcZa1ViQRnClz3VTjk9cnomIds3AFhsiNihNTPVSirbeCOKxr99mvJuuGdarzfkNEIbOkSLFfO7P6HdQHQjaTg9LueWUy1tz1gh0dsNpg4zPVr+T9lTCTWOnDgU2hNixo0r9wo72dxwXTc55vX4X7sWSz29WzrlKyyBQ2+CcA55EYp6cWwpkaTSfV+Boymr/ZnLI7qlp/7FGZk2574fvE/9uCZdnAHYCTzKOFUjEwZ9o8sw/f+TVglbKvRDSMpqsZXN6DY7FvXMp52ACM7OAp63y8Hir2YKAWj6OJ8KVoS8TAUeDmHyaWwwIDAQAB';
	$nofityData = decryptRSA($content, $publicKey);
	
	echo '{"code":0,"msg":"success"}';
	
	function decryptRSA($source, $key) {
		$pubPem = chunk_split($key, 64, "\n");
		$pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
		$public_key = openssl_pkey_get_public($pubPem); 
		if(!$public_key){
			die('invalid public key');
		}
		$decrypted='';//decode must be done before spliting for getting the binary String
		$data=str_split(base64_decode($source),256);
		foreach($data as $chunk){
			$partial = '';//be sure to match padding
			$decryptionOK = openssl_public_decrypt($chunk,$partial,$public_key,OPENSSL_PKCS1_PADDING);
			if($decryptionOK===false){die('fail');}
				$decrypted.=$partial;
			}
		return $decrypted;
	}
?>