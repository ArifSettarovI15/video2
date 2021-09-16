<?php
function catch_db_error($errno, $errstr, $errfile, $errline)
{
    global $Main;
    static $failures;

    if (strstr($errstr, 'Lost connection') AND $failures < 5) {
        $failures++;
        return;
    }

    if (is_object($Main->db)) {
        $Main->db->halt("$errstr\r\n$errfile on line $errline");
    } else {
        TW_error_handler($errno, $errstr, $errfile, $errline);
    }
}
function sendTestSMS($to, $text)
{
    send_sms_mail($to, $text);
}
function generateCode($length) {
    $chars = "123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}
function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}
function TW_error_handler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
        case E_WARNING:
        case E_USER_WARNING:
            if (!error_reporting() OR !ini_get('display_errors')) {
                return;
            }
            $errfile = str_replace(ROOT_DIR, '[path]', $errfile);
            $errstr = str_replace(ROOT_DIR, '[path]', $errstr);
            echo "<br /><strong>Warning</strong>: $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
            break;

        case E_USER_ERROR:
            if (!headers_sent()) {
                if (php_sapi_name() == 'cgi' OR php_sapi_name() == 'cgi-fcgi') {
                    header('Status: 500 Internal Server Error');
                } else {
                    header('HTTP/1.1 500 Internal Server Error');
                }
            }

            if (error_reporting() OR ini_get('display_errors')) {
                $errfile = str_replace(ROOT_DIR, '[path]', $errfile);
                $errstr = str_replace(ROOT_DIR, '[path]', $errstr);
                echo "<br /><strong>Fatal error:</strong> $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
                if (function_exists('debug_print_backtrace')) {
                    echo str_repeat(' ', 512);
                    debug_print_backtrace();
                }
            }
            exit;
            break;
    }
}

function htmlspecialchars_uni($text, $entities = true)
{
    return str_replace(
        array('<', '>', '"'),
        array('&lt;', '&gt;', '&quot;'),
        preg_replace(
            '/&(?!' . ($entities ? '#[0-9]+|shy' : '(#[0-9]+|[a-z]+)') . ';)/si',
            '&amp;',
            $text
        )
    );
}
function fetch_random_string($length = 32)
{
    $hash = sha1(TIMENOW . SESSION_HOST . microtime() . uniqid(mt_rand(), true) . implode('', @fstat(fopen( __FILE__, 'r'))));

    return substr($hash, 0, $length);
}
function GenerateName($length=32,$level=3){

    $validchars[0] = "abcdfghjkmnpqrstvwxyz";
    $validchars[1] = "0123456789";
    $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyz";
    $validchars[3] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $validchars[4] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

    $password  = "";
    $counter   = 0;

    while ($counter < $length) {
        $actChar = substr($validchars[$level], mt_rand(0, strlen($validchars[$level])-1), 1);
        $password .= $actChar;
        $counter++;
    }

    return $password;
}

function Set_Cookie($name, $value = '',$expire = false, $path='/',$domain='', $allowsecure = true, $httponly = false)
{
    if ($expire==false)
    {
        $expire = TIMENOW + 60 * 60 * 24 * 365;
    }

    $secure = ((REQ_PROTOCOL === 'https' AND $allowsecure) ? true : false);

    ExecSetcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

}

function ExecSetcookie($name, $value, $expires, $path = '', $domain = '', $secure = false, $httponly = false)
{
    if ($httponly AND $value)
    {
        // cookie names and values may not contain any of the characters listed
        foreach (array(",", ";", " ", "\t", "\r", "\n", "\013", "\014") AS $bad_char)
        {
            if (strpos($name, $bad_char) !== false OR strpos($value, $bad_char) !== false)
            {
                return false;
            }
        }

        // name and value
        $cookie = "Set-Cookie: $name=" . urlencode($value);

        // expiry
        $cookie .= ($expires > 0 ? '; expires=' . gmdate('D, d-M-Y H:i:s', $expires) . ' GMT' : '');

        // path
        $cookie .= ($path ? "; path=$path" : '');

        // domain
        $cookie .= ($domain ? "; domain=$domain" : '');

        // secure
        $cookie .= ($secure ? '; secure' : '');

        // httponly
        $cookie .= ($httponly ? '; HttpOnly' : '');

        header($cookie, false);
        return true;
    }
    else
    {
        return setcookie($name, $value, $expires, $path, $domain, $secure);
    }
}

function Redirect_to ($url, $statusCode = 303){
    header('Location: ' . $url, true, $statusCode);
    die();
}
function SiteRedirect ($path='', $statusCode = 303){
    $url=BASE_URL.'/'.$path;
    Redirect_to($url, $statusCode);
}

function is_valid_email($email)
{
    return preg_match('#^[a-zа-я0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s\'"<>@,;]+\.+[a-zа-я]{2,6}))$#si', $email);
}

function ChangeEncode ($from,$to,$value){
    if (function_exists('iconv'))
    {
        return  iconv($from, $to, $value);
    }
    elseif (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($value, $to, $from);
    }
  return $value;
}

function GetTimeName ($pattern,$timestamp){
    $value=strftime($pattern,$timestamp);
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
       $value=ChangeEncode("cp1251", "UTF-8", strftime($pattern));
    }
    return $value;
}

function ConvertRuMonth ($id){
    $months = array(1 => 'января', 2 => 'февраля', 3 => 'марта', 	4 => 'апреля',
        5 => 'июля', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');
    return $months[$id];
}



function insert_base64_encoded_image_src($img, $echo = false){
    $imageSize = getimagesize($img);
    $imageData = base64_encode(file_get_contents($img));
    $imageSrc = "data:{$imageSize['mime']};base64,{$imageData}";
    if($echo == true){
        echo $imageSrc;
    } else {
        return $imageSrc;
    }
}

function CutHeadText ($data,$cut=255) {
    $data=trim(strip_tags($data));
    if (strlen($data)>255) {
        $data=substr($data,0,$cut);
        $jjj=strrpos ($data,' ');

        $data=substr($data,0,$jjj);
        if (substr($data,strlen($data)-1,1)==',') {
            $data=substr($data,0,strlen($data)-1);
        }
        $data.='...';


    }
    return $data;
}

function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    }
    else {
        // Return array
        return $d;
    }
}
function arrayToObject($d) {
    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return (object) array_map(__FUNCTION__, $d);
    }
    else {
        // Return object
        return $d;
    }
}

function load_url($url, $parametr=array(),$time=30) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time);
    curl_setopt($ch, CURLOPT_TIMEOUT, $time);
    if (preg_match_all('#https://#Us', $url, $res)) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    if ($parametr['agent']) {
        curl_setopt($ch, CURLOPT_USERAGENT, $parametr['agent']);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    if ($parametr['referer']!="") {
        curl_setopt($ch, CURLOPT_REFERER, $parametr['referer']);
    }

    if ($parametr['cookie']!="") {
        curl_setopt($ch, CURLOPT_COOKIE, $parametr['cookie']);
    }
    elseif ($parametr['cookie_file']!="") {
        curl_setopt ($ch, CURLOPT_COOKIEJAR, $parametr['cookie_file']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $parametr['cookie_file']);
    }

    if(is_array($parametr['post']) && count($parametr['post'])>0)
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parametr['post']);
    }

    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

function translit_url_safe ($title) {
    $title=urlpath_safe(translit($title));
    if (strlen($title)>250) {
        $title=substr($title,0,250 );
    }
    return $title;
}
function translit_file_safe ($title) {
    $title=filename_safe(translit($title));
    return $title;
}
function filename_safe($filename) {
    $temp = $filename;

// Lower case
    $temp = strtolower($temp);

// Replace spaces with a '_'
    $temp = str_replace(" ", "_", $temp);

// Loop through string
    $result = '';
    for ($i=0; $i<strlen($temp); $i++) {
        if (preg_match('([0-9]|[a-z]|_)', $temp[$i])) {
            $result = $result . $temp[$i];
        }
    }

// Return filename
    return $result;
}

function urlpath_safe($filename) {
    $temp = $filename;

// Lower case
    $temp = strtolower($temp);

// Replace spaces with a '_'
    $temp = str_replace(" ", "-", $temp);
    $temp = str_replace("/", "_", $temp);

// Loop through string
    $result = '';
    for ($i=0; $i<strlen($temp); $i++) {
        if (preg_match('([0-9]|[a-z]|_|.|-)', $temp[$i])) {
            $result = $result . $temp[$i];
        }
    }
    $result=str_replace('_','-',$result);
    $result = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $result)));
    return $result;
}
function translit($text) {
    $trans = array(
        "а" => "a",
        "б" => "b",
        "в" => "v",
        "г" => "g",
        "д" => "d",
        "е" => "e",
        "ё" => "e",
        "ж" => "zh",
        "з" => "z",
        "и" => "i",
        "й" => "y",
        "к" => "k",
        "л" => "l",
        "м" => "m",
        "н" => "n",
        "о" => "o",
        "п" => "p",
        "р" => "r",
        "с" => "s",
        "т" => "t",
        "у" => "u",
        "ф" => "f",
        "х" => "kh",
        "ц" => "ts",
        "ч" => "ch",
        "ш" => "sh",
        "щ" => "shch",
        "ы" => "y",
        "э" => "e",
        "ю" => "yu",
        "я" => "ya",
        "А" => "A",
        "Б" => "B",
        "В" => "V",
        "Г" => "G",
        "Д" => "D",
        "Е" => "E",
        "Ё" => "E",
        "Ж" => "Zh",
        "З" => "Z",
        "И" => "I",
        "Й" => "Y",
        "К" => "K",
        "Л" => "L",
        "М" => "M",
        "Н" => "N",
        "О" => "O",
        "П" => "P",
        "Р" => "R",
        "С" => "S",
        "Т" => "T",
        "У" => "U",
        "Ф" => "F",
        "Х" => "Kh",
        "Ц" => "Ts",
        "Ч" => "Ch",
        "Ш" => "Sh",
        "Щ" => "Shch",
        "Ы" => "Y",
        "Э" => "E",
        "Ю" => "Yu",
        "Я" => "Ya",
        "ь" => "",
        "Ь" => "",
        "ъ" => "",
        "Ъ" => "",
    );
    if(preg_match("/[а-яА-Я]/", $text)) {
        return strtr($text, $trans);
    }
    else {
        return $text;
    }
}

function RuMonth($Num,$type=1) {
    if ($type==1) {
        $MonthNames = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
    }
    else {
        $MonthNames = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "Декабря");
    }
    return $MonthNames[$Num-1];
}

function CleanSpaces ($string) {
    $string=trim($string);
    $string=str_replace('  ',' ',$string);
    $string=str_replace('  ',' ',$string);
    $string=str_replace('  ',' ',$string);
    $string=str_replace('  ',' ',$string);
    $string=str_replace('  ',' ',$string);
    $string=str_replace('  ',' ',$string);
    $string = trim(preg_replace('/\s+/', ' ', $string));
    return $string;
}

function CleanValue ($value) {
    $value=trim(strip_tags(htmlspecialchars_decode($value)));
    $value=CleanSpaces($value);
    return $value;
}

function format_money ($value) {
	return number_format($value, 0, '', ' ');
}

function GetDirListRecursive ($start_dir, $find_dir) {
    $array=array();
    if (is_dir($find_dir)) {
        $Open = opendir($find_dir);
        while ($Files = readdir($Open)) {
            if ($Files != '.' && $Files != '..') {
                $file_path = $find_dir . "/" . $Files;
                if (is_file($file_path)) {
                    $array[] =str_replace($start_dir,'',$file_path);
                }
                elseif (is_dir($file_path)) {
                    $array=array_merge($array,GetDirListRecursive ($start_dir,$file_path));
                }
            }
        }
        closedir($Open);
    }
    return $array;
}

function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $ending=$endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending=$endingArray[2];
        }
    }
    return $ending;
}


if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}
