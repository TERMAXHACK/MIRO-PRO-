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
$from = 'Explore';
$mid = bot('sendMessage',[
		'chat_id'=>$id,
		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
	])->result->message_id;
$ids = [];
$posts = [];
for($i=0;$i<20;$i++){
	  $explore = curl_init();
		curl_setopt($explore, CURLOPT_URL, "https://i.instagram.com/api/v1/discover/explore/?is_prefetch=false&max_id=$i&module=explore_popular&timezone_offset=10800&session_id=db7d7ac9-4605-4855-ad5e-d16edc561934 ");
	  curl_setopt($explore, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($explore, CURLOPT_HTTPHEADER, array(
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
	  $res = curl_exec($explore);
	  curl_close($explore);
	  $json = json_decode($res);
	  foreach($json->items as $item){
	  	$posts[] = $item->media->pk;
	  }
        	bot('editmessageText',[
        		'chat_id'=>$id,
        		'message_id'=>$mid,
        		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Posts* ~ ".count($posts)."\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
        		'parse_mode'=>'markdown',
        		'reply_markup'=>json_encode(['inline_keyboard'=>[
        				[['text'=>'Stop Grabber','callback_data'=>'stopgr']]
        			]])
        	]);
	  echo "Done $i ~ ".count($posts).PHP_EOL;
	  sleep(1);
}
$ids = [];
$for = file_get_contents('for');
$i = 0;
$e = 43;

foreach($posts as $post){
	$comments = curl_init();
	curl_setopt($comments, CURLOPT_URL, "https://i.instagram.com/api/v1/media/".$post."/comments/");
	curl_setopt($comments, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($comments, CURLOPT_HTTPHEADER, array(
	    'x-ig-capabilities: 3w==',
	    'host: i.instagram.com',
	    'X-CSRFToken: missing',
	    'X-Instagram-AJAX: 1',
	    'Content-Type: application/x-www-form-urlencoded',
	    'X-Requested-With: XMLHttpRequest','Cookie: '.$accounts[$file]['cookies'],
			'User-Agent: '.$accounts[$file]['useragent'],
	    'Connection: keep-alive'
	));
	$res = curl_exec($comments);
	curl_close($comments);
	$comments = json_decode($res)->comments;
	foreach($comments as $comment){
		if(!in_array($comment->user->username, $ids)){
			$ids[] = $comment->user->username;
      file_put_contents($file, $comment->user->username."\n",FILE_APPEND);
      echo $comment->user->username."\n";
      if($i == $e){
      	$from = file_exists('from') ? file_get_contents('from') : 'From Search';
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
			if($i > 5000){
				exit;
			}
			$i++;
		}
	}
	// username(1);
}
bot('sendMessage',[
		'chat_id'=>$id,
		'reply_to_message_id'=>$mid,
		'text'=>"*Done Collection . * \n All : ".count(explode("\n", file_get_contents($file))),
		'parse_mode'=>'markdown',
]);
