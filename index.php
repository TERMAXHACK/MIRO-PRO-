<?php
function status($for){
    if($for == '1'){
        $x = exec('screen -S mails1 -Q select . ; echo $?');
    } elseif($for == '2'){
        $x = exec('screen -S mails2 -Q select . ; echo $?');
    }
    if($x == '0'){
        return 'Running.';
    } else {
        return 'Stopped.';
    }
    
}
function checkMail($mail){
$mail = strtolower($mail);
        if(mb_substr($mail, -10) === '@gmail.com'){
            return checkGmail($mail);
        } elseif(preg_match('/(hotmail|outlook)/', $mail)){
            return checkHotmail(newURL(),$mail);
        } elseif(strpos($mail, 'yahoo.com')){
            return checkYahoo($mail);
        } elseif(preg_match('/(mail|bk|yandex|inbox|list)\.(ru)/i', $mail)){
            return checkRU($mail);
        } elseif(strpos($mail, 'aol.com')){
            	return checkAol($mail);
        } else {
            return false;
            
        }
}
function bot($method,$datas=[]){
    global $token;
$url = "https://api.telegram.org/bot".$token."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function inInsta($mail){
    $mail = strtolower($mail);
  $search = curl_init(); 
curl_setopt($search, CURLOPT_URL, "https://i.instagram.com/api/v1/users/lookup/"); 
curl_setopt($search, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($search, CURLOPT_ENCODING , "");
curl_setopt($search, CURLOPT_HTTPHEADER, explode("\n", 'Host: i.instagram.com
Connection: keep-alive
X-IG-Connection-Type: WIFI
X-IG-Capabilities: 3Ro=
Accept-Language: en-US
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
User-Agent: Instagram 9.7.0 Android (28/9; 420dpi; 1080x2131; samsung; SM-A505F; a50; exynos9610; en_US)
Accept-Encoding: gzip, deflate
t'));
curl_setopt($search,CURLOPT_POST, 1);
$fields = 'signed_body=acd10e3607b478b845184ff7af8d796aec14425d5f00276567ea0876b1ff2630.%7B%22_csrftoken%22%3A%22rZj5Y3kci0OWbO8AMUi0mWwcBnUgnJDY%22%2C%22q%22%3A%22'.urlencode($mail).'%22%2C%22_uid%22%3A%226758469524%22%2C%22guid%22%3A%22a475d908-a663-4895-ac60-c0ab0853d6df%22%2C%22device_id%22%3A%22android-1a9898fad127fa2a%22%2C%22_uuid%22%3A%22a475d908-a663-4895-ac60-c0ab0853d6df%22%7D&ig_sig_key_version=4';
curl_setopt($search,CURLOPT_POSTFIELDS, $fields);
$search = curl_exec($search);
// echo $search;
$search = json_decode($search);
    if($search->status != 'fail'){
        if($search->can_email_reset == true){
            return ['fb'=>$search->fb_login_option,'ph'=>$search->has_valid_phone];
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function getInfo($id,$cookies,$useragent){
$search = curl_init(); 
curl_setopt($search, CURLOPT_URL, "https://i.instagram.com/api/v1/users/".trim($id)."/usernameinfo/"); 
curl_setopt($search, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($search, CURLOPT_ENCODING , "");
curl_setopt($search, CURLOPT_TIMEOUT, 15);
$h = explode("\n", 'Host: i.instagram.com
Connection: keep-alive
X-IG-Connection-Type: WIFI
X-IG-Capabilities: 3Ro=
Accept-Language: en-US
Cookie: '.$cookies.'
User-Agent: '.$useragent.'
Accept-Encoding: gzip, deflate, sdch');
curl_setopt($search, CURLOPT_HTTPHEADER, $h);
$search = curl_exec($search);
// echo $search;
$search = json_decode($search);

if(isset($search->user)){
    $user = $search->user;
$ret = ['f'=>$user->follower_count,'ff'=>$user->following_count,'m'=>$user->media_count,'user'=>$user->username];
if(isset($user->public_email)){
  if($user->public_email != ''){
      $mail = $user->public_email;
      $ret['mail'] = $mail;
  } else {
      $ret = false;
  }
} else {
  $ret = false;
}
} elseif($search->message){
    if($search->message == 'Please wait a few minutes before you try again.' or $search->message == 'challenge_required'){
        $ret = 'checkpoint';
        usleep (888888);
    } else {
        echo json_encode($search);    
    }
} else {
    echo json_encode($search);
    $ret = false;
}
return $ret;
}
function newURL(){
  $url = 'https://login.live.com/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $get = curl_exec($ch);
  curl_close($ch);
  preg_match("/\:'https\:\/\/login.live.com\/GetCredentialType(.*)',/", $get,$m);
  $url = explode("',", $m[0])[0];
  $url = str_replace(':\'', '',$url);
  return $url;
}
function checkRU($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') or strpos($mail, '+')){
        return false;
    }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"https://auth.mail.ru/api/v1/pushauth/info?login=".urlencode($mail)."&_=1580336451166");
  curl_setopt($ch,CURLOPT_HTTPHEADER, [
    'Host: recostream.go.mail.ru',
'Connection: keep-alive',
'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
'Accept: */*',
'Origin: https://mail.ru',
'Sec-Fetch-Site: same-site',
'Sec-Fetch-Mode: cors',
'Referer: https://mail.ru/',
'Accept-Encoding: gzip, deflate, br',
'Accept-Language: en-US,en;q=0.9,ar;q=0.8'
    ]);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $res = curl_exec($ch);
  curl_close($ch);
//   return ;
    if(!json_decode($res)->body->exists) {
        return true;
    } else {
        return false;
    }
}
function checkAol($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') or strpos($mail, '+')){
        return false;
    }
$user = $mail;
@mkdir("Info2");
$c = curl_init("https://login.aol.com/"); 
curl_setopt($c, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36"); 
curl_setopt($c, CURLOPT_REFERER, 'https://www.google.com'); 
curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');  
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($c, CURLOPT_HEADER, true); 
curl_setopt($c, CURLOPT_COOKIEJAR, "Info2/cookie.txt"); 
curl_setopt($c, CURLOPT_COOKIEFILE, "Info2/cookie.txt"); 
$response = curl_exec($c); 
$httpcode = curl_getinfo($c); 
$header = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE)); 
$body = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE)); 
preg_match_all('#name="crumb" value="(.*?)" />#', $response, $crumb); 
preg_match_all('#name="acrumb" value="(.*?)" />#', $response, $acrumb); 
preg_match_all('#name="config" value="(.*?)" />#', $response, $config); 
preg_match_all('#name="sessionIndex" value="(.*?)" />#', $response, $sesindex); 
$data['status'] = "ok"; 
$data['crumb'] = isset($crumb[1][0]) ? $crumb[1][0] : ""; 
$data['acrumb'] = $acrumb[1][0]; 
$data['config'] = isset($config[1][0]) ? $config[1][0] : ""; 
$data['sesindex'] = $sesindex[1][0]; 
$crumb = trim($data['crumb']); 
$acrumb = trim($data['acrumb']); 
$config = trim($data['config']); 
$sesindex = trim($data['sesindex']); 
$header = array(); 
$header[] = "Host: login.aol.com"; 
$header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0"; 
$header[] = "Accept: */*"; 
$header[] = "Accept-Language: en-US,en;q=0.5"; 
$header[] = "content-type: application/x-www-form-urlencoded; charset=UTF-8"; 
$header[] = "X-Requested-With: XMLHttpRequest"; 
$header[] = "Referer: https://login.aol.com/"; 
$header[] = "Connection: keep-alive"; 
$data = "acrumb=$acrumb&sessionIndex=$sesindex&username=".urlencode($user)."&passwd=&signin=Next"; 
$c = curl_init("https://login.aol.com/"); 
curl_setopt($c, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36"); 
curl_setopt($c, CURLOPT_REFERER, 'https://login.aol.com/'); 
curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');  
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($c, CURLOPT_HTTPHEADER, $header); 
curl_setopt($c, CURLOPT_COOKIEJAR, "Info2/cookie.txt"); 
curl_setopt($c, CURLOPT_COOKIEFILE, "Info2/cookie.txt"); 
curl_setopt($c, CURLOPT_POSTFIELDS, $data); 
curl_setopt($c, CURLOPT_POST, 1); 
$b = curl_exec($c); 
if(strstr($b,"INVALID_USERNAME")){
echo "ues";
return true;
}else{
echo "no";
return false;
}
}
function checkYahoo($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') or strpos($mail, '+')){
        return false;
    }
$user = $mail;
@mkdir("Info");
$c = curl_init("https://login.yahoo.com/"); 
curl_setopt($c, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36"); 
curl_setopt($c, CURLOPT_REFERER, 'https://www.google.com'); 
curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');  
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($c, CURLOPT_HEADER, true); 
curl_setopt($c, CURLOPT_COOKIEJAR, "Info/cookie.txt"); 
curl_setopt($c, CURLOPT_COOKIEFILE, "Info/cookie.txt"); 
$response = curl_exec($c); 
$httpcode = curl_getinfo($c); 
$header = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE)); 
$body = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE)); 
preg_match_all('#name="crumb" value="(.*?)" />#', $response, $crumb); 
preg_match_all('#name="acrumb" value="(.*?)" />#', $response, $acrumb); 
preg_match_all('#name="config" value="(.*?)" />#', $response, $config); 
preg_match_all('#name="sessionIndex" value="(.*?)" />#', $response, $sesindex); 
$data['status'] = "ok"; 
$data['crumb'] = isset($crumb[1][0]) ? $crumb[1][0] : ""; 
$data['acrumb'] = $acrumb[1][0]; 
$data['config'] = isset($config[1][0]) ? $config[1][0] : ""; 
$data['sesindex'] = $sesindex[1][0]; 
$crumb = trim($data['crumb']); 
$acrumb = trim($data['acrumb']); 
$config = trim($data['config']); 
$sesindex = trim($data['sesindex']); 
$header = array(); 
$header[] = "Host: login.yahoo.com"; 
$header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:85.0) Gecko/20100101 Firefox/85.0"; 
$header[] = "Accept: */*"; 
$header[] = "Accept-Language: en-US,en;q=0.5"; 
$header[] = "content-type: application/x-www-form-urlencoded; charset=UTF-8"; 
$header[] = "X-Requested-With: XMLHttpRequest"; 
$header[] = "Referer: https://login.yahoo.com/"; 
$header[] = "Connection: keep-alive"; 
$data = "acrumb=$acrumb&sessionIndex=$sesindex&username=".urlencode($user)."&passwd=&signin=Next"; 
$c = curl_init("https://login.yahoo.com/"); 
curl_setopt($c, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:85.0) Gecko/20100101 Firefox/85.0"); 
curl_setopt($c, CURLOPT_REFERER, 'https://login.yahoo.com/'); 
curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');  
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($c, CURLOPT_HTTPHEADER, $header); 
curl_setopt($c, CURLOPT_COOKIEJAR, "Info/cookie.txt"); 
curl_setopt($c, CURLOPT_COOKIEFILE, "Info/cookie.txt"); 
curl_setopt($c, CURLOPT_POSTFIELDS, $data); 
curl_setopt($c, CURLOPT_POST, 1); 
$b = curl_exec($c); 
if(strstr($b,"INVALID_USERNAME")){
return true;
}else{
return false;
}
}
function verifyEmail($email){
    $ip = file_get_contents("ip.txt");
    $gmail = json_decode(file_get_contents("http://$ip/api/gmail.php?email=$email"),true)["result"]["success"];
    if($gmail){
      return false;
    }else{
     return true;
  }

}
function check_ban($gmail){
    $gmail = str_replace("@gmail.com", "", $gmail);
    $data = "{\"input01\":{\"Input\":\"GmailAddress\",\"GmailAddress\":\"".$gmail."\",\"FirstName\":\"JKHack\",\"LastName\":\"JKHack\"},\"Locale\":\"en\"}";
    
    $header = array(); 
    $header[] = "User-Agent: Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16"; 
    $header[] = "content-type: application/json; charset=utf-8"; 
    $c = curl_init("https://accounts.google.com/InputValidator?resource=SignUp&service=mail"); 
    curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16"); 
    curl_setopt($c, CURLOPT_HTTPHEADER, $header); 
    curl_setopt($c, CURLOPT_COOKIEJAR, "sessions/Gcookie.txt"); 
    curl_setopt($c, CURLOPT_COOKIEFILE, "sessions/Gcookie.txt"); 
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_POST, 1); 
    $b = curl_exec($c);
    curl_close($c);
    
    if(preg_match('/"Valid":"true"/', $b)){

   $s = "Yes";
    }else{
      $s = "No";
    }
    return $s;
  }
function checkGmail($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') or strpos($mail, '+')){
        return false;
    }
  $mail = preg_replace('/@(.*)/', '',$mail);
   $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,'https://accounts.google.com/InputValidator?resource=SignUp&service=mail');
  curl_setopt($ch,CURLOPT_HTTPHEADER, [
    'User-Agent: generate User agent ',
'Content-Type: application/json; charset=utf-8',
'Host: accounts.google.com',
'Expect: 100-continue',
    ]);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_ENCODING , "");
  // echo $mail;
  $fields = '{"input01":{"Input":"GmailAddress","GmailAddress":"'.$mail.'","FirstName":"'.str_shuffle('fdgh4hgbgbg').'","LastName":"'.str_shuffle('fdgh4hgbgbg').'"},"Locale":"en"}';
  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
  $res = curl_exec($ch);
  curl_close($ch);
  $s =  json_decode($res);
  if(isset($s->input01)){
  if(isset($s->input01->Valid)){
      if($s->input01->Valid == 'true'){
          return true;
      } else {
          return false;
      }
  } else {
      return false;
  }
  } else {
      return false;
  }
}
function checkHotmail($url,$mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') or strpos($mail, '+')){
        return false;
    }
  $uaid = explode('uaid=', $url)[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_HTTPHEADER, [
    'accept: application/json',
'accept-encoding: gzip, deflate, br',
'accept-language: en-US,en;q=0.9,ar;q=0.8',
'client-request-id: e50b9d86940a4a6b806f141aeb87c2be',
'content-type: application/json; charset=UTF-8',
'cookie: mkt=en-GB; MSCC=1565316440; optimizelyEndUserId=oeu1578914839745r0.28780916970876746; wlidperf=FR=L&ST=1578914863298; logonLatency=LGN01=637153910513160953; uaid=e50b9d86940a4a6b806f141aeb87c2be; amsc=LDSu01eN1p8mu/aQOR8E/JsrWRw2umolJ57H96YKK9t9GpXT/1+TnnHT5teMGz0XmgPXf4UZumsU54kipsswO6VwZggyEEZkxrR8SJd5U3Bru+OEs+9IlLfml8nsNJ3ejH7piSM6y5EfybxtuLMV6SZZxPrFEODePzRujEx/dSV7jpiSYTNk/oajPVQIoZbABA+Hr8QjedZ5390TM7sQmrIwwSPfbUP9vTrTPwnm6GAsbf1k90qWSLMaldKhMPKz1IZCPvKBdWxmfda1hcHSkitzm2byDrC8a0LpF2XtGKG1rZ9S+WvSILthbvLn7tHD:2:3c; MSPRequ=id=N&lt=1579804236&co=0; OParams=11DQFpxS7pzYB5u6z67WXLWoJZxIv4EoI07SIv9NF400Ml6NW3t6RoWfW5Hr7lizMq9bTQDRrsBBlbQXkVL!Jzo6knJIEJdFbUDS!Cq1zNJJNK1ehiYyB5fMyO7bnj7Dfz!6mDuk2OShJVVlatli5JeYXDDFRljVvQzkJ91cXbHLJoRP9A!EbyBF3boCkZ7s9f*ePQZWGwqnAeCz3sclT68b4ntJXMLTAqi4CgcEiEE9XjSekdGg2q!pHh7IcjwLKjvusYzdiaK6axwAp4hw35vvcsyA4UOD26uE04LKjAFPIDZcXmrqzHNjklndRTqAp!1PMSFEvdlrAa9FyrbN1f6CA$; MSPOK=$uuid-84fae358-0e4d-4cbc-9401-c4c0d1dfc0b8$uuid-2dfff29d-11fd-4e53-85a7-8d3cff5e2754$uuid-b7c92f16-b89a-445d-95a9-cc1c6686aab2',
'hpgact: 0',
'hpgid: 33',
'origin: https://login.live.com',
'referer: https://login.live.com/',
'sec-fetch-mode: cors',
'sec-fetch-site: same-origin',
'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'
    ]);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_ENCODING , "");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_POST, 1);
  curl_setopt($ch,CURLOPT_POSTFIELDS, '{"username":"'.$mail.'","uaid":"'.$uaid.'","isOtherIdpSupported":false,"checkPhones":true,"isRemoteNGCSupported":true,"isCookieBannerShown":false,"isFidoSupported":true,"forceotclogin":false,"otclogindisallowed":true,"isExternalFederationDisallowed":false,"isRemoteConnectSupported":false,"federationFlags":3,"flowToken":"DdMUDCNyFcwT9VK5vlBBCGF5VYFUBuVVVK2FCJkTvdIr8vao!78DWHV1d5iJQAlaBgKQtik4V0TTdj0gqiYx89skmL*Ir9FvzAs8FIul6MJmsHl*WMZuh0WOAYNDzGgH!5A9TURocDSg*qbkZVrdh1ZG0j5NWvtsfdqMRYbAqujacfOSUA2ZuxmvSFlYz3dxOG3DhusRzPYqFqfWhc3xLxFDzf4NhhCCPTdQ3BQfvcZ9yE0KqqOWnDllRJvXO!tJeA$$"}');
  $res = curl_exec($ch);
  curl_close($ch);
  $res = json_decode($res)->IfExistsResult;
  if($res == 1){
      return true;
  } else {
      return false;
  }
}
class EzTGException extends Exception
{
}
class EzTG
{
    private $settings;
    private $offset;
    private $json_payload;
    public function __construct($settings, $base = false)
    {
        $this->settings = array_merge(array(
      'endpoint' => 'https://api.telegram.org',
      'token' => '1234:abcd',
      'callback' => function ($update, $EzTG) {
          echo 'no callback' . PHP_EOL;
      },
      'objects' => true,
      'allow_only_telegram' => true,
      'throw_telegram_errors' => true,
      'magic_json_payload' => false
    ), $settings);
        if ($base !== false) {
            return true;
        }
        if (!is_callable($this->settings['callback'])) {
            $this->error('Invalid callback.', true);
        }
        if (php_sapi_name() === 'cli') {
            $this->settings['magic_json_payload'] = false;
            $this->offset = -1;
            $this->get_updates();
        } else {
            if ($this->settings['allow_only_telegram'] === true and $this->is_telegram() === false) {
                http_response_code(403);
                echo '403 - You are not Telegram,.,.';
                return 'Not Telegram';
            }
            if ($this->settings['magic_json_payload'] === true) {
                ob_start();
                $this->json_payload = false;
                register_shutdown_function(array($this, 'send_json_payload'));
            }
            if ($this->settings['objects'] === true) {
                $this->processUpdate(json_decode(file_get_contents('php://input')));
            } else {
                $this->processUpdate(json_decode(file_get_contents('php://input'), true));
            }
        }
    }
    private function is_telegram()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { //preferisco non usare x-forwarded-for xk si può spoof
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (($ip >= '149.154.160.0' && $ip <= '149.154.175.255') || ($ip >= '91.108.4.0' && $ip <= '91.108.7.255')) { //gram'''s ip : https://core.telegram.org/bots/webhooks
            return true;
        } else {
            return false;
        }
    }
    private function get_updates()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'] . '/bot' . $this->settings['token'] . '/getUpdates');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        while (true) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'offset=' . $this->offset . '&timeout=10');
            if ($this->settings['objects'] === true) {
                $result = json_decode(curl_exec($ch));
                if (isset($result->ok) and $result->ok === false) {
                    $this->error($result->description, false);
                } elseif (isset($result->result)) {
                    foreach ($result->result as $update) {
                        if (isset($update->update_id)) {
                            $this->offset = $update->update_id + 1;
                        }
                        $this->processUpdate($update);
                    }
                }
            } else {
                $result = json_decode(curl_exec($ch), true);
                if (isset($result['ok']) and $result['ok'] === false) {
                    $this->error($result['description'], false);
                } elseif (isset($result['result'])) {
                    foreach ($result['result'] as $update) {
                        if (isset($update['update_id'])) {
                            $this->offset = $update['update_id'] + 1;
                        }
                        $this->processUpdate($update);
                    }
                }
            }
        }
    }
    public function processUpdate($update)
    {
        $this->settings['callback']($update, $this);
    }
    protected function error($e, $throw = 'default')
    {
        if ($throw === 'default') {
            $throw = $this->settings['throw_telegram_errors'];
        }
        if ($throw === true) {
            throw new EzTGException($e);
        } else {
            echo 'Telegram error: ' . $e . PHP_EOL;
            return array(
        'ok' => false,
        'description' => $e
      );
        }
    }
    public function newKeyboard($type = 'keyboard', $rkm = array('resize_keyboard' => true, 'keyboard' => array()))
    {
        return new EzTGKeyboard($type, $rkm);
    }
    public function __call($name, $arguments)
    {
        if (!isset($arguments[0])) {
            $arguments[0] = array();
        }
        if (!isset($arguments[1])) {
            $arguments[1] = true;
        }
        if ($this->settings['magic_json_payload'] === true and $arguments[1] === true) {
            if ($this->json_payload === false) {
                $arguments[0]['method'] = $name;
                $this->json_payload = $arguments[0];
                return 'json_payloaded'; //xd
            } elseif (is_array($this->json_payload)) {
                $old_payload = $this->json_payload;
                $arguments[0]['method'] = $name;
                $this->json_payload = $arguments[0];
                $name = $old_payload['method'];
                $arguments[0] = $old_payload;
                unset($arguments[0]['method']);
                unset($old_payload);
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'] . '/bot' . $this->settings['token'] . '/' . urlencode($name));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments[0]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->settings['objects'] === true) {
            $result = json_decode(curl_exec($ch));
        } else {
            $result = json_decode(curl_exec($ch), true);
        }
        curl_close($ch);
        if ($this->settings['objects'] === true) {
            if (isset($result->ok) and $result->ok === false) {
                return $this->error($result->description);
            }
            if (isset($result->result)) {
                return $result->result;
            }
        } else {
            if (isset($result['ok']) and $result['ok'] === false) {
                return $this->error($result['description']);
            }
            if (isset($result['result'])) {
                return $result['result'];
            }
        }
        return $this->error('Unknown error', false);
    }
    public function send_json_payload()
    {
        if (is_array($this->json_payload)) {
            ob_end_clean();
            echo json_encode($this->json_payload);
            header('Content-Type: application/json');
            ob_end_flush();
            return true;
        }
    }
}
class EzTGKeyboard
{
    public function __construct($type = 'keyboard', $rkm = array('resize_keyboard' => true, 'keyboard' => array()))
    {
        $this->line = 0;
        $this->type = $type;
        if ($type === 'inline') {
            $this->keyboard = array(
        'inline_keyboard' => array()
      );
        } else {
            $this->keyboard = $rkm;
        }
        return $this;
    }
    public function add($text, $callback_data = null, $type = 'auto')
    {
        if ($this->type === 'inline') {
            if ($callback_data === null) {
                $callback_data = trim($text);
            }
            if (!isset($this->keyboard['inline_keyboard'][$this->line])) {
                $this->keyboard['inline_keyboard'][$this->line] = array();
            }
            if ($type === 'auto') {
                if (filter_var($callback_data, FILTER_VALIDATE_URL)) {
                    $type = 'url';
                } else {
                    $type = 'callback_data';
                }
            }
            array_push($this->keyboard['inline_keyboard'][$this->line], array(
        'text' => $text,
        $type => $callback_data
      ));
        } else {
            if (!isset($this->keyboard['keyboard'][$this->line])) {
                $this->keyboard['keyboard'][$this->line] = array();
            }
            array_push($this->keyboard['keyboard'][$this->line], $text);
        }
        return $this;
    }
    public function newline()
    {
        $this->line++;
        return $this;
    }
    public function done()
    {
        if ($this->type === 'remove') {
            return '{"remove_keyboard": true}';
        } else {
            return json_encode($this->keyboard);
        }
    }
}
class ig {
	private $url = 'https://i.instagram.com/api/v1';
	private $account;
	private $ret = [];
	private $file;
	public function __construct($settings){
		$this->account = $settings['account'];
		$this->account['useragent'] = 'Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)';
		$this->file = $settings['file'];
		
	}
	public function login($user,$pass){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request('accounts/login/',
			0,
			1,
			[
				'signed_body'=>'57afc5aa6cc94675a08329beaffaec7bad237df0198ed801280f459e80095abb.'.json_encode([
					'phone_id'=>$guid,
					'username'=>$user,
					'_uid'=>rand(1000000000,9999999999),
					'guid'=>$guid,
					'_uuid'=>$guid,
					'device_id'=>'android-'.$guid,
					'password'=>$pass,
					'login_attempt_count'=>'0',
				])
			],
			1
		);
	}
	public function news(){
		return $this->request('news/inbox/',1);
	}
	
	public function getComments($mediaId){
    return $this->request("media/{$mediaId}/comments/",1,0,['can_support_threading'=>true]);
  }
  public function getLikers($mediaId){
	  return $this->request("media/{$mediaId}/likers/",1);
	}
	public function getPosts($userId){
		return $this->request("feed/user/{$userId}/",1);
	}
	public function getInfo($username){
		return $this->request("users/{$username}/usernameinfo/",1)->user;
	}
	public function comment($mediaId,$comment){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request("media/{$mediaId}/comment/",1,1,[
						'user_breadcrumb'=>$this->generateUserBreadcrumb(mb_strlen($comment)),
            'idempotence_token'=>$uuid,
            '_uuid'=>$uuid,
            '_uid'=>rand(1000000000,9999999999),
            'comment_text'=>$comment,
            'containermodule'=>'comments_feed_timeline',
            'radio_type'=>'wifi-none'
		]);
	}
	public function like($mediaId){
		$uuid = $this->UUID();
		$guid = $this->GUID();
    return $this->request("media/{$mediaId}/like/",1,1,[
        '_uuid'=>$uuid,
        '_uid'=>rand(1000000000,9999999999),
        'media_id'=>$mediaId,
        'radio_type'=>'wifi-none',
        'module_name'=>'feed_timeline'
    ]);
  }
	public function unfollow($id){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request("friendships/destroy/$id/",1,1,[
					'_uid'=>rand(1000000000,9999999999),
					'_uuid'=>$guid,
					'user_id'=>$id,
					'radio_type'=>'wifi-none'
		]);
	}
	public function follow($id){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request("friendships/create/$id/",1,1,[
					'_uid'=>rand(1000000000,9999999999),
					'_uuid'=>$guid,
					'user_id'=>$id,
					'radio_type'=>'wifi-none'
		]);
	}
	public function sign_in_help($email)
  {
      $data = ['username_or_email'=>$email,'_uuid'=>$this->UUID()];
      return $this->request('accounts/sign_in_help/',1,1,$data);
  }
	public function getFollowing($id,$mid,$uuu,$maxId = null){
	    $config = json_decode(file_get_contents('config.json'),1);
	    $from = 'Following.';
		$file = $this->file;
		$rank_token = $this->UUID();
		$datas['rank_token'] = $rank_token;
		if($maxId != null){
			$datas['max_id'] = $maxId;
		}
		$res = $this->request("friendships/$id/following/",1,0,$datas,0);
		if(isset($res->users)){
			$in = explode("\n",file_get_contents($file));
			foreach($res->users as $user){
				if(!in_array($user->username, $in)){
					$users[] = $user->username;
					file_put_contents($file, $user->username."\n",FILE_APPEND);
				}
			}
    	
    	bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'message_id'=>$mid,
    		'text'=>"*𝐂𝐎𝐋𝐋𝐄𝐂𝐓𝐈𝐎𝐍 𝐅𝐑𝐎𝐌 †* ~ [ _ $from _ ]\n\n*𝐒𝐓𝐀𝐓𝐔𝐒  †* ~> _ 𝐖𝐎𝐑𝐊𝐈𝐍𝐆  _\n*𝐔𝐒𝐄𝐑𝐒 †* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'𝐒𝐓𝐎𝐏 †.','callback_data'=>'stopgr']]
		]])
    	]);
		}
		if($res->next_max_id != null){
			$this->getFollowing($id,$mid,$uuu,$res->next_max_id);
		} else {
			bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'message_id'=>$mid,
    		'text'=>"*𝐂𝐎𝐋𝐋𝐄𝐂𝐓𝐈𝐎𝐍 𝐅𝐑𝐎𝐌 †* ~ [ _ $from _ ]\n\n*𝐒𝐓𝐀𝐓𝐔𝐒  †* ~> _ 𝐖𝐎𝐑𝐊𝐈𝐍𝐆  _\n*𝐔𝐒𝐄𝐑𝐒 †* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'𝐒𝐓𝐎𝐏 †.','callback_data'=>'stopgr']]
		]])
    	]);
    	bot('sendMessage',[
    		'chat_id'=>$config['id'],
    		'reply_to_message_id'=>$mid,
    		'text'=>"*𝗗𝗢𝗡𝗘 𝗖𝗢𝗟𝗟𝗘𝗖𝗧𝗜𝗢𝗡 . * \n 𝐀𝐋𝐋  : ".count(explode("\n", file_get_contents($file))),
    		'parse_mode'=>'markdown',
        ]);
		}
	}
	public function getFollowers($id,$mid,$uuu,$maxId = null){
	    $config = json_decode(file_get_contents('config.json'),1);
	    $from = 'Followers';
		$file = $this->file;
		$rank_token = $this->UUID();
		$datas['rank_token'] = $rank_token;
		if($maxId != null){
			$datas['max_id'] = $maxId;
		}
		$res = $this->request("friendships/$id/followers/",1,0,$datas,0);
		if(isset($res->users)){
			$in = explode("\n",file_get_contents($file));
			foreach($res->users as $user){
				if(!in_array($user->username, $in)){
					$users[] = $user->username;
					file_put_contents($file, $user->username."\n",FILE_APPEND);
				}
				}
    	
    	bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'message_id'=>$mid,
    		'text'=>"*𝐂𝐎𝐋𝐋𝐄𝐂𝐓𝐈𝐎𝐍 𝐅𝐑𝐎𝐌 †* ~ [ _ $from _ ]\n\n*𝐒𝐓𝐀𝐓𝐔𝐒  †* ~> _ 𝐖𝐎𝐑𝐊𝐈𝐍𝐆  _\n*𝐔𝐒𝐄𝐑𝐒 †* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'𝐒𝐓𝐎𝐏 †.','callback_data'=>'stopgr']]
		]])
    	]);
		}
		if($res->next_max_id != null){
			$this->getFollowers($id,$mid,$uuu,$res->next_max_id);
		} else {
			bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'text'=>"*𝐂𝐎𝐋𝐋𝐄𝐂𝐓𝐈𝐎𝐍 𝐅𝐑𝐎𝐌 †* ~ [ _ $from _ ]\n\n*𝐒𝐓𝐀𝐓𝐔𝐒  †* ~> _ 𝐖𝐎𝐑𝐊𝐈𝐍𝐆  _\n*𝐔𝐒𝐄𝐑𝐒 †* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'𝐒𝐓𝐎𝐏 †.','callback_data'=>'stopgr']]
		]])
    	]);
    	bot('sendMessage',[
    		'chat_id'=>$config['id'],
    		'reply_to_message_id'=>$mid,
    		'text'=>"*𝗗𝗢𝗡𝗘 𝗖𝗢𝗟𝗟𝗘𝗖𝗧𝗜𝗢𝗡 . * \n 𝐀𝐋𝐋  : ".count(explode("\n", file_get_contents($file))),
    		'parse_mode'=>'markdown',
        ]);
		}
	}
	
	
	public function bot($method,$datas=[]){
    $token = file_get_contents('token');
		$url = "https://api.telegram.org/bot".$token."/".$method;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
		$res = curl_exec($ch);
		
		if(curl_error($ch)){
			return curl_error($ch);
		}else{
		    
			return json_decode($res);
		}
	}
	public function generateUserBreadcrumb($size){
      $key = 'iN4$aGr0m';
      $date = (int) (microtime(true) * 1000);
      $term = rand(2, 3) * 1000 + $size * rand(15, 20) * 100;
      $text_change_event_count = round($size / rand(2, 3));
      if ($text_change_event_count == 0) {
          $text_change_event_count = 1;
      }
      $data = $size.' '.$term.' '.$text_change_event_count.' '.$date;
      return base64_encode(hash_hmac('sha256', $data, $key, true))."\n".base64_encode($data)."\n";
  }
	private function GUID(){
    if (function_exists('com_create_guid') === true){
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	private function UUID(){
    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
    
    return $uuid;
	}
	private function request($path,$account = 0,$post = 0,$datas = 0,$returnHeaders = 0){
		$ch = curl_init(); 
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  if($post == 1){
	  	curl_setopt($ch, CURLOPT_POST, 1);
	  }
	  if($datas != 0 and $post == 1){
		  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
		  curl_setopt($ch, CURLOPT_URL, $this->url .'/'. $path); 
	  } elseif($datas != 0 and $post == 0){
	  	curl_setopt($ch, CURLOPT_URL, $this->url .'/'. $path.'?'.http_build_query($datas)); 
	  } else {
	  	curl_setopt($ch, CURLOPT_URL, $this->url .'/'. $path); 
	  }
	  if($account == 0){
	  	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		     'x-ig-capabilities: 3w==',
		     'user-agent: Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)',
		     'host: i.instagram.com',
		     'X-CSRFToken: missing',
		     'X-Instagram-AJAX: 1',
		     'Content-Type: application/x-www-form-urlencoded',
		     'X-Requested-With: XMLHttpRequest',
		     "Cookie: mid=XUzLlQABAAH63ME45I6TG-i46cOi",
		     'Connection: keep-alive'
		  ));
	  } elseif($account == 1){
	  	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	      'x-ig-capabilities: 3w==',
	      'user-agent: '.$this->account['useragent'],
	      'host: i.instagram.com',
	      'X-CSRFToken: missing',
	      'X-Instagram-AJAX: 1',
	      'Content-Type: application/x-www-form-urlencoded',
	      'X-Requested-With: XMLHttpRequest',
	      "Cookie: ".$this->account['cookies'],
	      'Connection: keep-alive'
	  ));
	  }
	  if($returnHeaders == 1){
		  curl_setopt($ch, CURLOPT_HEADER, 1);
		  $res = curl_exec($ch);
		  $res = explode("\r\n\r\n", $res);
	  } else {
		  $res = curl_exec($ch);
		  $res = json_decode($res);
	  }
	  return $res;
	}
}
