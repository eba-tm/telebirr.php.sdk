<?php
	header('Content-Type:application/json; charset=utf-8');
	$api = 'http://ip:port/service-openup/toTradeWebPay';
	$appkey = 'a8955b02b5df475882038616d5448d43';
	$publicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgIwN9mVEWG9kagbxt2ippr8RNzK/fhBXcZa1ViQRnClz3VTjk9cnomIds3AFhsiNihNTPVSirbeCOKxr99mvJuuGdarzfkNEIbOkSLFfO7P6HdQHQjaTg9LueWUy1tz1gh0dsNpg4zPVr+T9lTCTWOnDgU2hNixo0r9wo72dxwXTc55vX4X7sWSz29WzrlKyyBQ2+CcA55EYp6cWwpkaTSfV+Boymr/ZnLI7qlp/7FGZk2574fvE/9uCZdnAHYCTzKOFUjEwZ9o8sw/f+TVglbKvRDSMpqsZXN6DY7FvXMp52ACM7OAp63y8Hir2YKAWj6OJ8KVoS8TAUeDmHyaWwwIDAQAB';

	$data=[
		'outTradeNo' => $_POST['outTradeNo'],
		'subject' => $_POST['subject'],
		'totalAmount' => $_POST['totalAmount'],
		'shortCode' => $_POST['shortCode'],
		'notifyUrl' => $_POST['notifyUrl'],
		'returnUrl' => $_POST['returnUrl'],
		'receiveName' => $_POST['receiveName'],
		'appId' => $_POST['appid'],
		'timeoutExpress' => $_POST['timeoutExpress'],
		'nonce' => $_POST['nonce'],
		'timestamp' => $_POST['timestamp']
    ];
	ksort($data);
	$ussd = $data;
	$data['appKey'] = $appkey;
	ksort($data);
	$sign = sign($data);
	$encode = [
		'appid' => $data['appId'],
		'sign' => $sign['sha256'],
		'ussd' => encryptRSA(json_encode($ussd),$publicKey)
	];
	
	$result = [
		'encode' => json_encode($encode),
		'sign' => $sign['values'],
		'ussd' => json_encode($ussd)
	];
	echo json_encode($result);
	
	function sign($params){
		$signPars = '';
		
		foreach($params as $k => $v){
			if($signPars == ''){
				$signPars = $k.'='.$v;
			}else{
				$signPars = $signPars.'&'.$k.'='.$v;
			}
		}
		$sign = [
			'sha256' => hash("sha256", $signPars),
			'values' => $signPars
		];
		return $sign;
	}
	
	function encryptRSA($data, $public){
		$pubPem = chunk_split($public, 64, "\n");
		$pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
		
		$public_key = openssl_pkey_get_public($pubPem); 
		if(!$public_key){
			die('invalid public key');
		}
		$crypto = '';
		foreach(str_split($data, 117) as $chunk){
			$return = openssl_public_encrypt($chunk, $cryptoItem, $public_key);
			if(!$return){
				return('fail');
			}
			$crypto .= $cryptoItem;
		}
		$ussd = base64_encode($crypto);
		return $ussd;
	}
?>