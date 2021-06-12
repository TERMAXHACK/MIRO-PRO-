<?php
$chars = range('a', 'z');
$nums = range(0,9);
$chars = array_merge($chars, $nums);
$i = 0;
$e = 15;
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
$from = 'Search';
$mid = bot('sendMessage',[
		'chat_id'=>$id,
		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
	])->result->message_id;
foreach($words as $word){
foreach($chars as $char){
$word1 = $word."$char";
$word1 = urlencode($word1);
$search = curl_init(); 
curl_setopt($search, CURLOPT_URL, "https://www.instagram.com/web/search/topsearch/?query=$word1"); 
curl_setopt($search, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($search, CURLOPT_ENCODING , "");
curl_setopt($search, CURLOPT_HTTPHEADER, [
				'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'accept-language: en-US,en;q=0.9',
				'cache-control: max-age=0',
				'cookie: '.$accounts[$file]['cookies'],
				'user-agent: '.$accounts[$file]['useragent']
			]);
$search = curl_exec($search);
$search = json_decode($search);
$aa = [];
$for = $config['for'];
foreach($search->users as $user){
	$user = $user->user;
	if(in_array($user->username, explode("\n", file_get_contents($file)))){
		continue;
	}
  file_put_contents($file, $user->username."\n",FILE_APPEND);
	echo $i.PHP_EOL;
	$i++;
	if($i == $e){
	echo 'edit..';
	$from = 'البحث';
	bot('editmessageText',[
		'chat_id'=>$id,
		'message_id'=>$mid,
		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
			]])
	]);
	$e += 25;
}
}
}
foreach($chars as $char){
	
$word2='';
$word2 = urlencode($char.$word);
$search = curl_init(); 
curl_setopt($search, CURLOPT_URL, "https://www.instagram.com/web/search/topsearch/?query=$word2"); 
curl_setopt($search, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($search, CURLOPT_ENCODING , "");
curl_setopt($search, CURLOPT_HTTPHEADER, [
				'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'accept-language: en-US,en;q=0.9',
				'cache-control: max-age=0',
				'cookie: '.$accounts[$file]['cookies'],
				'user-agent: '.$accounts[$file]['useragent']
			]);
$search = curl_exec($search);
$search = json_decode($search);
$aa = [];
foreach($search->users as $user){
	$user = $user->user;
	if(in_array($user->username, explode("\n", file_get_contents($file)))){
		continue;
	}
  file_put_contents($file, $user->username."\n",FILE_APPEND);
	echo $i.PHP_EOL;
	$i++;
	if($i == $e){
	echo 'edit..';
	$from = 'البحث';
	bot('editmessageText',[
		'chat_id'=>$id,
		'message_id'=>$mid,
		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
	]);
	$e += 25;
}
}

}
}
bot('sendMessage',[
		'chat_id'=>$id,
		'reply_to_message_id'=>$mid,
		'text'=>"*Done Collection . * \n All : ".count(explode("\n", file_get_contents($file))),
		'parse_mode'=>'markdown',
]);

