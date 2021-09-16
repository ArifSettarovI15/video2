<?php
/**
 * Created by TigerWeb

 * Date: 15.07.2015
 * Time: 20:02
 */
define('QUERY_TYPE_SELECT', 0);
define('QUERY_TYPE_FIRST', 1);
define('QUERY_TYPE_COUNT', 2);
define('QUERY_TYPE_DELETE', 3);
define('QUERY_TYPE_UPDATE', 4);
define('QUERY_TYPE_INSERT', 5);
define('QUERY_TYPE_CUSTOM', 6);
define('QUERY_TYPE_MINMAX', 7);

define('TYPE_NOCLEAN', 0); // no change

define('TYPE_BOOL', 1); // force boolean
define('TYPE_INT', 2); // force integer
define('TYPE_UINT', 3); // force unsigned integer
define('TYPE_NUM', 4); // force number
define('TYPE_UNUM', 5); // force unsigned number
define('TYPE_UNIXTIME', 6); // force unix datestamp (unsigned integer)
define('TYPE_STR', 7); // TYPE_UINT trimmed string
define('TYPE_NOTRIM', 8); // force string - no trim
define('TYPE_NOHTML', 9); // force trimmed string with HTML made safe
define('TYPE_ARRAY', 10); // force array
define('TYPE_FILE', 11); // force file
define('TYPE_BINARY', 12); // force binary string
define('TYPE_NOHTMLCOND', 13); // force trimmed string with HTML made safe if determined to be unsafe

define('TYPE_ARRAY_BOOL', 101);
define('TYPE_ARRAY_INT', 102);
define('TYPE_ARRAY_UINT', 103);
define('TYPE_ARRAY_NUM', 104);
define('TYPE_ARRAY_UNUM', 105);
define('TYPE_ARRAY_UNIXTIME', 106);
define('TYPE_ARRAY_STR', 107);
define('TYPE_ARRAY_NOTRIM', 108);
define('TYPE_ARRAY_NOHTML', 109);
define('TYPE_ARRAY_ARRAY', 110);
define('TYPE_ARRAY_FILE', 11);  // An array of "Files" behaves differently than other <input> arrays. TYPE_FILE handles both types.
define('TYPE_ARRAY_BINARY', 112);
define('TYPE_ARRAY_NOHTMLCOND', 113);

define('TYPE_ARRAY_KEYS_INT', 202);
define('TYPE_ARRAY_KEYS_STR', 207);

define('TYPE_CONVERT_SINGLE', 100); // value to subtract from array types to convert to single types
define('TYPE_CONVERT_KEYS', 200); // value to subtract from array => keys types to convert to single types

// temporary
define('INT', TYPE_INT);
define('STR', TYPE_STR);
define('STR_NOHTML', TYPE_NOHTML);
define('FILE', TYPE_FILE);




class Input_Cleaner
{
    var $superglobal_lookup = array(
        'g' => '_GET',
        'p' => '_POST',
        'r' => '_REQUEST',
        'c' => '_COOKIE',
        's' => '_SERVER',
        'e' => '_ENV',
        'f' => '_FILES'
    );

    var $scriptpath = '';
    var $reloadurl = '';
    var $url = '';
    var $ipaddress = '';
    var $alt_ip = '';
    var $registry;
    var $cleaned_vars = array();
    
    function Input_Cleaner(&$registry)
    {
        $this->registry =& $registry;

        if (!is_array($GLOBALS)) {
            die('<strong>Fatal Error:</strong> Invalid URL.');
        }

        // overwrite GET[x] and REQUEST[x] with POST[x] if it exists (overrides server's GPC order preference)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach (array_keys($_POST) AS $key) {
                if (isset($_GET["$key"])) {
                    $_GET["$key"] = $_REQUEST["$key"] = $_POST["$key"];
                }
            }
        }

        // reverse the effects of magic quotes if necessary
        if (function_exists('get_magic_quotes_gpc')) {
            $this->stripslashes_deep($_REQUEST); // needed for some reason (at least on php5 - not tested on php4)
            $this->stripslashes_deep($_GET);
            $this->stripslashes_deep($_POST);
            $this->stripslashes_deep($_COOKIE);

            if (is_array($_FILES)) {
                foreach ($_FILES AS $key => $val) {
                    $_FILES["$key"]['tmp_name'] = str_replace('\\', '\\\\', $val['tmp_name']);
                }
                $this->stripslashes_deep($_FILES);
            }
        }
        @ini_set('magic_quotes_sybase', 0);

        foreach (array('_GET', '_POST') AS $arrayname) {
            if (isset($GLOBALS["$arrayname"]['do'])) {
                $GLOBALS["$arrayname"]['do'] = trim($GLOBALS["$arrayname"]['do']);
            }
        }

        // set the AJAX flag if we have got an AJAX submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $_POST['ajax'] = $_REQUEST['ajax'] = 1;
        }

        // reverse the effects of register_globals if necessary
        if (@ini_get('register_globals') OR !@ini_get('gpc_order')) {
            foreach ($this->superglobal_lookup AS $arrayname) {
                $registry->superglobal_size["$arrayname"] = sizeof($GLOBALS["$arrayname"]);

                foreach (array_keys($GLOBALS["$arrayname"]) AS $varname) {
                    // make sure we dont unset any global arrays like _SERVER
                    if (!in_array($varname, $this->superglobal_lookup)) {
                        unset($GLOBALS["$varname"]);
                    }
                }
            }
        } else {
            foreach ($this->superglobal_lookup AS $arrayname) {
                $registry->superglobal_size["$arrayname"] = sizeof($GLOBALS["$arrayname"]);
            }
        }

        // deal with cookies that may conflict with _GET and _POST data, and create our own _REQUEST with no _COOKIE input
        foreach (array_keys($_COOKIE) AS $varname) {
            unset($_REQUEST["$varname"]);
            if (isset($_POST["$varname"])) {
                $_REQUEST["$varname"] =& $_POST["$varname"];
            } else if (isset($_GET["$varname"])) {
                $_REQUEST["$varname"] =& $_GET["$varname"];
            }
        }

        $this->ipaddress = $this->fetch_ip();
        $this->alt_ip = $this->fetch_alt_ip();
        $this->scriptpath = $this->fetch_scriptpath();

        // fetch url of current page without the variable string
        $quest_pos = strpos($this->scriptpath, '?');
        if ($quest_pos !== false) {
            $this->script = substr($this->scriptpath, 0, $quest_pos);
        } else {
            $this->script = $this->scriptpath;
        }


    }

    function stripslashes_deep(&$value, $depth = 0)
    {
        if (is_array($value)) {
            foreach ($value AS $key => $val) {
                if (is_string($val)) {
                    $value["$key"] = stripslashes($val);
                } else if (is_array($val) AND $depth < 10) {
                    $this->stripslashes_deep($value["$key"], $depth + 1);
                }
            }
        }
    }

    /**
     * Fetches the IP address of the current visitor
     *
     * @return    string
     */
    function fetch_ip()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    function fetch_alt_ip()
    {
        $alt_ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            // make sure we dont pick up an internal IP defined by RFC1918
            foreach ($matches[0] AS $ip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $ip)) {
                    $alt_ip = $ip;
                    break;
                }
            }
        } else if (isset($_SERVER['HTTP_FROM'])) {
            $alt_ip = $_SERVER['HTTP_FROM'];
        }

        return $alt_ip;
    }

    function fetch_scriptpath()
    {

        if ($this->scriptpath != '') {
            return $this->scriptpath;
        } else {
            if ($_SERVER['REQUEST_URI'] OR $_ENV['REQUEST_URI']) {
                $scriptpath = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_ENV['REQUEST_URI'];
            } else {
                if ($_SERVER['PATH_INFO'] OR $_ENV['PATH_INFO']) {
                    $scriptpath = $_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO'] : $_ENV['PATH_INFO'];
                } else if ($_SERVER['REDIRECT_URL'] OR $_ENV['REDIRECT_URL']) {
                    $scriptpath = $_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_ENV['REDIRECT_URL'];
                } else {
                    $scriptpath = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_ENV['PHP_SELF'];
                }

                if ($_SERVER['QUERY_STRING'] OR $_ENV['QUERY_STRING']) {
                    $scriptpath .= '?' . ($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_ENV['QUERY_STRING']);
                }
            }

            // in the future we should set $registry->script here too
            $quest_pos = strpos($scriptpath, '?');
            if ($quest_pos !== false) {
                $script = urldecode(substr($scriptpath, 0, $quest_pos));
                $scriptpath = $script . substr($scriptpath, $quest_pos);
            } else {
                $scriptpath = urldecode($scriptpath);
            }

            // store a version that includes the sessionhash
            $this->reloadurl = $this->xss_clean($scriptpath);

            $scriptpath = $this->xss_clean($scriptpath);
            $this->scriptpath = $scriptpath;

            return $scriptpath;
        }
    }


    function xss_clean($var)
    {
        $preg_find = array('#^javascript#i');
        $preg_replace = array('java script');

        return preg_replace($preg_find, $preg_replace, htmlspecialchars(trim($var)));
    }

    function &clean_array(&$source, $variables)
    {
        $return = array();

        foreach ($variables AS $varname => $vartype) {
            $return["$varname"] =& $this->clean($source["$varname"], $vartype, isset($source["$varname"]));
        }

        return $return;
    }


    function &clean(&$var, $vartype = TYPE_NOCLEAN, $exists = true)
    {
        if ($exists) {
            if ($vartype < TYPE_CONVERT_SINGLE) {
                $this->do_clean($var, $vartype);
            } else if (is_array($var)) {
                if ($vartype >= TYPE_CONVERT_KEYS) {
                    $var = array_keys($var);
                    $vartype -= TYPE_CONVERT_KEYS;
                } else {
                    $vartype -= TYPE_CONVERT_SINGLE;
                }

                foreach (array_keys($var) AS $key) {
                    $this->do_clean($var["$key"], $vartype);
                }
            } else {
                $var = array();
            }
            return $var;
        } else {
            if ($vartype < TYPE_CONVERT_SINGLE) {
                switch ($vartype) {
                    case TYPE_INT:
                    case TYPE_UINT:
                    case TYPE_NUM:
                    case TYPE_UNUM:
                    case TYPE_UNIXTIME: {
                        $var = 0;
                        break;
                    }
                    case TYPE_STR:
                    case TYPE_NOHTML:
                    case TYPE_NOTRIM:
                    case TYPE_NOHTMLCOND: {
                        $var = '';
                        break;
                    }
                    case TYPE_BOOL: {
                        $var = 0;
                        break;
                    }
                    case TYPE_ARRAY:
                    case TYPE_FILE: {
                        $var = array();
                        break;
                    }
                    case TYPE_NOCLEAN: {
                        $var = null;
                        break;
                    }
                    default: {
                        $var = null;
                    }
                }
            } else {
                $var = array();
            }

            return $var;
        }
    }

    function &do_clean(&$data, $type)
    {
        static $booltypes = array('1', 'yes', 'y', 'true');

        switch ($type) {
            case TYPE_INT:
                $data = intval($data);
                break;
            case TYPE_UINT:
                $data = ($data = intval($data)) < 0 ? 0 : $data;
                break;
            case TYPE_NUM:
                $data = floatval($data) + 0;
                break;
            case TYPE_UNUM:
                $data = floatval($data) + 0;
                $data = ($data < 0) ? 0 : $data;
                break;
            case TYPE_BINARY:
                $data = strval($data);
                break;
            case TYPE_STR:
                $data = trim(strval($data));
                break;
            case TYPE_NOTRIM:
                $data = strval($data);
                break;
            case TYPE_NOHTML:
                $data = htmlspecialchars_uni(trim(strval($data)));
                break;
            case TYPE_BOOL:
                $data = in_array(strtolower($data), $booltypes) ? 1 : 0;
                break;
            case TYPE_ARRAY:
                $data = (is_array($data)) ? $data : array();
                break;
            case TYPE_NOHTMLCOND: {
                $data = trim(strval($data));
                if (strcspn($data, '<>"') < strlen($data) OR (strpos($data, '&') !== false AND !preg_match('/&(#[0-9]+|amp|lt|gt|quot);/si', $data))) {
                    // data is not htmlspecialchars because it still has characters or entities it shouldn't
                    $data = htmlspecialchars_uni($data);
                }
                break;
            }
            case TYPE_FILE: {
                // perhaps redundant :p
                if (is_array($data)) {
                    if (is_array($data['name'])) {
                        $files = count($data['name']);
                        for ($index = 0; $index < $files; $index++) {
                            $data['name']["$index"] = trim(strval($data['name']["$index"]));
                            $data['type']["$index"] = trim(strval($data['type']["$index"]));
                            $data['tmp_name']["$index"] = trim(strval($data['tmp_name']["$index"]));
                            $data['error']["$index"] = intval($data['error']["$index"]);
                            $data['size']["$index"] = intval($data['size']["$index"]);
                        }
                    } else {
                        $data['name'] = trim(strval($data['name']));
                        $data['type'] = trim(strval($data['type']));
                        $data['tmp_name'] = trim(strval($data['tmp_name']));
                        $data['error'] = intval($data['error']);
                        $data['size'] = intval($data['size']);
                    }
                } else {
                    $data = array(
                        'name' => '',
                        'type' => '',
                        'tmp_name' => '',
                        'error' => 0,
                        'size' => 4, // UPLOAD_ERR_NO_FILE
                    );
                }
                break;
            }
            case TYPE_UNIXTIME: {
                if (is_array($data)) {
                    $data = $this->clean($data, TYPE_ARRAY_UINT);
                    if ($data['month'] AND $data['day'] AND $data['year']) {
                        $data = mktime($data['hour'], $data['minute'], $data['second'], $data['month'], $data['day'], $data['year']);
                    } else {
                        $data = 0;
                    }
                } else {
                    $data = ($data = intval($data)) < 0 ? 0 : $data;
                }
                break;
            }
            // null actions should be defined here so we can still catch typos below
            case TYPE_NOCLEAN: {
                break;
            }

            default: {
                if ($this->registry->debug) {
                    trigger_error('Input_Cleaner::do_clean() Invalid data type specified', E_USER_WARNING);
                }
            }
        }

        // strip out characters that really have no business being in non-binary data
        switch ($type) {
            case TYPE_STR:
            case TYPE_NOTRIM:
            case TYPE_NOHTML:
            case TYPE_NOHTMLCOND:
                $data = str_replace(chr(0), '', $data);
        }

        return $data;
    }

    function clean_array_gpc($source, $variables)
    {
        $sg =& $GLOBALS[$this->superglobal_lookup["$source"]];

        foreach ($variables AS $varname => $vartype) {
            // clean a variable only once unless its a different type
            if (!isset($this->cleaned_vars["$varname"]) OR $this->cleaned_vars["$varname"] != $vartype) {
                $this->registry->GPC_exists["$varname"] = isset($sg["$varname"]);
                $this->registry->GPC["$varname"] =& $this->clean(
                    $sg["$varname"],
                    $vartype,
                    isset($sg["$varname"])
                );
                $this->cleaned_vars["$varname"] = $vartype;
            }
        }
    }

    function &clean_gpc($source, $varname, $vartype = TYPE_NOCLEAN)
    {
        // clean a variable only once unless its a different type
        if (!isset($this->cleaned_vars["$varname"]) OR $this->cleaned_vars["$varname"] != $vartype) {
            $sg =& $GLOBALS[$this->superglobal_lookup["$source"]];

            $this->registry->GPC_exists["$varname"] = isset($sg["$varname"]);
            $this->registry->GPC["$varname"] =& $this->clean(
                $sg["$varname"],
                $vartype,
                isset($sg["$varname"])
            );
            $this->cleaned_vars["$varname"] = $vartype;
        }

        return $this->registry->GPC["$varname"];
    }


    function fetch_url()
    {
        $temp_url = $_REQUEST['url'];

        $scriptpath = $this->fetch_scriptpath();

        if (empty($temp_url)) {
            $url = $_SERVER['HTTP_REFERER'];
        } else {
            if ($temp_url == $_SERVER['HTTP_REFERER']) {
                $url = 'index.php';
            } else {
                $url = $temp_url;
            }
        }

        if ($url == $scriptpath OR empty($url)) {
            $url = 'index.php';
        }

        $url = $this->xss_clean($url);

        return $url;
    }
}
