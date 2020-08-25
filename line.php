<?php

$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = 'kCPJ9QBcMlg/SnBCoBG6MTWDAvdX4fcn3Xia+QUay4cbkj8CCErP8ASTWIgp8NQG1a8DdrZKqyr3+bENAFT65uQZD8P2tTkMwjAmi4XOOmamTGkokkZfr/Mmh8MiIkMwwyGcsGiH41LjjYQsF22YhwdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

if ( sizeof($request_array['events']) > 0 )
{

 foreach ($request_array['events'] as $event)
 {
  $reply_message = '';
  $reply_token = $event['replyToken'];

  if ( $event['type'] == 'message' ) 
  {
   
   if( $event['message']['type'] == 'text' )
   {
		$text = $event['message']['text'];
	   	$str_msg = explode(" ",$text);
	   	if($str_msg[0] == "@บอท"){ 

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://thaiqa.p.rapidapi.com/predict",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "[    {      \"paragraphs\": [        {          \"qas\": [            {              \"id\": \"1\",              \"question\": \"ราคาทอง"              }          ],          \"context\": \"ราคาทองวันนี้"        }      ]    }]",
			CURLOPT_HTTPHEADER => array(
				"accept: application/json",
				"content-type: application/json",
				"x-rapidapi-host: thaiqa.p.rapidapi.com",
				"x-rapidapi-key: b6d6da9e6cmsh25aa4a1d7f13dafp11743fjsn87b01fa6001c"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			echo $response;
			$reply_message = $response;
		}
		}else	   
	   	if($text == "CDMA"){
			$reply_message = '1, -3, -1, -1';
		}else
		if($text == "ชื่อ" || $text == "ชื่ออะไร" || $text == "ชื่ออะไรงับ" || $text == "ชื่อผู้พัฒนา" ){
			$reply_message = 'ชื่อ นายธนเกียรติ หอมหวล รหัสนิสิต 61160061';
		}else 
	  	if($text == "เอฟ"){
			$reply_message = 'เอฟกินเยอะ';
		}else
	   	if($text == "สถานการณ์โควิดวันนี้" || $text == "covid19" || $text == "covid-19" || $text == "Covid-19"){
		     $url = 'https://covid19.th-stat.com/api/open/today';
		     $ch = curl_init($url);
		     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		     curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
		     curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
		     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		     $result = curl_exec($ch);
		     curl_close($ch);   

		     $obj = json_decode($result);

		     $reply_message = $result;
		     $reply_message = "ติดเชื้อสะสม ". $obj->{'Confirmed'} ." คน \nรักษาหายแล้ว ". $obj->{'Recovered'} ." คน";
 		 }else 		
			$reply_message = 'เราไม่เข้าใจคำถามของคุณ ('.$text.') ';   
			
	   
   }
   else
    $reply_message = 'ระบบได้รับ '.ucfirst($event['message']['type']).' ของคุณแล้ว';
  
  }
  else
   $reply_message = 'ระบบได้รับ Event '.ucfirst($event['type']).' ของคุณแล้ว';
 
  if( strlen($reply_message) > 0 )
  {
   //$reply_message = iconv("tis-620","utf-8",$reply_message);
   $data = [
    'replyToken' => $reply_token,
    'messages' => [['type' => 'text', 'text' => $reply_message]]
   ];
   $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

   $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
   echo "Result: ".$send_result."\r\n";
  }
 }
}

echo "OK";

function send_reply_message($url, $post_header, $post_body)
{
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 $result = curl_exec($ch);
 curl_close($ch);

 return $result;
}

?>
