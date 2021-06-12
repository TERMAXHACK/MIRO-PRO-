<?php
// $config = json_decode(file_get_contents('config.json'),1);
// $id = $config['id'];
// $token = $config['token'];
// include 'index.php';
// $accounts = json_decode(file_get_contents('accounts.json'),1);
// $id = $config['words'];
// $file = $config['for'];
// $a = file_exists('a') ? file_get_contents('a') : 'ap';
// if($a == 'new'){
// 	file_put_contents($file, '');
// }
// $ig = new ig(['account'=>$accounts[$file],'file'=>$file]);
$get = file_get_contents('https://www.instagram.com/p/CB5RaX3l6Gk/');
echo $get;
// preg_match('/"id"\:"(.*?)"/i',$get,$m);
// print_r($m);