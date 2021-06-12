<?php
$accounts = json_decode(file_get_contents('accounts.json'),1);
$config = json_decode(file_get_contents('config.json'),1);
$file = $config['for'];
$id = $config['id'];
$words = explode(' ',$config['words']);
$token = $config['token'];
include 'index.php';
$a = file_exists('a') ? file_get_contents('a') : 'ap';
if($a == 'new'){
	file_put_contents($file, '');
}
$from = 'HashTag';
$mid = bot('sendMessage',[
		'chat_id'=>$id,
		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
	])->result->message_id;
$tag = urlencode($config['words']);
$url = "https://i.instagram.com/api/v1/feed/tag/$tag/?rank_token=caf8d67a-5140-4fcd-a795-e2a9047dc5d9";
$ids = [];
$posts = [];
	$explore = curl_init();
	curl_setopt($explore, CURLOPT_URL, $url);
	curl_setopt($explore, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($explore, CURLOPT_HTTPHEADER, array(
							'Host: i.instagram.com',
							'Connection: keep-alive',
							'X-IG-Connection-Type: WIFI',
							'X-IG-Capabilities: 3Ro=',
							'Accept-Language: ar-AE',
							'Cookie: '.$accounts[$file]['cookies'],
							'User-Agent: '.$accounts[$file]['useragent']
					));
	$res = curl_exec($explore);
	curl_close($explore);
	$json = json_decode($res);
	file_put_contents('tag', json_encode($json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	foreach($json->ranked_items as $item){
		if(!in_array($item->id, $posts)){
			$posts[] = $item->id;
		}
	}
	echo "Done ~ ".count($posts).PHP_EOL;
$for = $config['for'];
$i = 0;
$e = 43;
foreach($posts as $post){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://i.instagram.com/api/v1/media/".$post."/likers/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'x-ig-capabilities: 3w==',
	    'host: i.instagram.com',
	    'X-CSRFToken: missing',
	    'X-Instagram-AJAX: 1',
	    'Content-Type: application/x-www-form-urlencoded',
	    'X-Requested-With: XMLHttpRequest',
	    'Cookie: '.$accounts[$file]['cookies'],
			'User-Agent: '.$accounts[$file]['useragent'],
	    'Connection: keep-alive'
	));
	$res = curl_exec($ch);
	$likers = json_decode($res)->users;
	curl_close($ch);
	foreach($likers as $user){
		if(!in_array($user->username, $ids)){
			$ids[] = $user->username;
			file_put_contents($file, $user->username."\n",FILE_APPEND);
			echo "$i ~ ".$user->username.PHP_EOL;
			if($i == $e){
        	bot('editmessageText',[
        		'chat_id'=>$id,
        		'message_id'=>$mid,
        		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
        	]);
        	$e += 43;
      }
			$i++;
		}
		
	}
	sleep(1);
}
bot('sendMessage',[
		'chat_id'=>$id,
		'reply_to_message_id'=>$mid,
		'text'=>"*Done Collection . * \n All : ".count(explode("\n", file_get_contents($file))),
		'parse_mode'=>'markdown',
]);

