<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
define('ROOT_DIR', realpath(dirname(__FILE__)."/.."));
define('REQ_PROTOCOL', ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']== 'https')) ? "https" : "http"));

if (isset($_SERVER['HTTP_HOST'])) {
	$server_url=REQ_PROTOCOL . "://" . $_SERVER['HTTP_HOST'];
}
else {
	$server_url="https://trans-crimea.com";
}
define('BASE_URL', $server_url);

//define('BASE_URL_RESERV',"https://trans-crimea.com");

if (strpos(BASE_URL,'www.' )) {
    header('Location: '.str_replace('www.','',BASE_URL.$_SERVER['REQUEST_URI']));
}



define('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
define('REFERRER', (isset( $_SERVER["HTTP_REFERER"] ))? $_SERVER['HTTP_REFERER'] : '');
define('SESSION_HOST', substr($_SERVER['REMOTE_ADDR'], 0, 15));
define('TIMESTART', microtime());
define('TIMENOW', time());

if (strpos(BASE_URL,'.tiger' )) {
	error_reporting(1);
	ini_set('display_errors', 1);
}
else {
	error_reporting(1);
	ini_set('display_errors', 1);
}
if (file_exists(ROOT_DIR . '/app/vendor/autoload.php')) {
    require_once ROOT_DIR . '/app/vendor/autoload.php';
}
else {
    die('Composer Error');
}

require_once ROOT_DIR.'/app/core/class_core.php';

set_error_handler('TW_error_handler');
$Main = new MainClass();

require_once ROOT_DIR . '/app/modules/users/init.php';
require_once ROOT_DIR . '/app/modules/lang/init.php';
require_once ROOT_DIR . '/app/modules/content/init.php';
require_once ROOT_DIR . '/app/modules/settings/init.php';
require_once ROOT_DIR . '/app/modules/routes/init.php';

require_once ROOT_DIR . '/app/modules/seo/init.php';

$Main->input->clean_array_gpc('r', array(
    'ajax' => TYPE_UINT
));
$Main->input->clean_array_gpc('c', array(
    'lang'=>TYPE_STR
));

require_once ROOT_DIR . '/app/modules/global/global.php';

$Main->route->ParseRoute($Main->GPC["route"]);


if (file_exists(ROOT_DIR . '/app/modules/' . $Main->route->mapping['module'].'/init.php')) {
    /** @noinspection PhpIncludeInspection */
	@require_once ROOT_DIR . '/app/modules/' . $Main->route->mapping['module'].'/init.php';

    if ($Main->route->mapping['action']!='') {
        /** @noinspection PhpIncludeInspection */
        @require_once ROOT_DIR . '/app/modules/' . $Main->route->mapping['module'].'/actions/'.$Main->route->mapping['action'];
    }
    else {
        $Main->error->ShowError('action_error');
    }
}
else {
    $Main->error->ShowError('module_error');
}


