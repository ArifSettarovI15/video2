<?php
/**
 * Created by TigerWeb

 * Date: 15.07.2015
 * Time: 20:00
 */


define('DBARRAY_BOTH', 0);
define('DBARRAY_ASSOC', 1);
define('DBARRAY_NUM', 2);

class DatabaseClass
{
    var $functions = array(
        'connect' => 'mysqli_real_connect',
        'pconnect' => 'mysqli_real_connect',
        'select_db' => 'mysqli_select_db',
        'query' => 'mysqli_query',
        'query_unbuffered' => 'mysqli_unbuffered_query',
        'fetch_row' => 'mysqli_fetch_row',
        'fetch_array' => 'mysqli_fetch_array',
        'fetch_field' => 'mysqli_fetch_field',
        'free_result' => 'mysqli_free_result',
        'data_seek' => 'mysqli_data_seek',
        'error' => 'mysqli_error',
        'errno' => 'mysqli_errno',
        'affected_rows' => 'mysqli_affected_rows',
        'num_rows' => 'mysqli_num_rows',
        'num_fields' => 'mysqli_num_fields',
        'field_name' => 'mysqli_field_tell',
        'insert_id' => 'mysqli_insert_id',
        'escape_string' => 'mysqli_real_escape_string',
        'real_escape_string' => 'mysqli_real_escape_string',
        'close' => 'mysqli_close',
        'client_encoding' => 'mysqli_client_encoding',
    );

    /**
     * @var MainClass
     */
    var $registry;

    var $fetchtypes = array(
        DBARRAY_NUM => MYSQLI_NUM,
        DBARRAY_ASSOC => MYSQLI_ASSOC,
        DBARRAY_BOTH => MYSQLI_BOTH
    );

    var $rows;
    var $database = null;
    var $connection_master = null;
    var $connection_recent = null;
    var $shutdownqueries = array();
    var $sql = '';
    var $error = '';
    var $errno = '';
    var $log_errors=true;
    var $maxpacket = 0;
    var $locked = false;
    var $querycount = 0;

    function __construct(&$registry)
    {
        $this->registry =& $registry;
    }

    /**
     * @param $database
     * @param $w_servername
     * @param $w_port
     * @param $w_username
     * @param $w_password
     * @param string $configfile
     * @param string $charset
     */
    function connect($database, $w_servername, $w_port, $w_username, $w_password, $configfile = '', $charset = '')
    {
        $this->database = $database;
        $w_port = $w_port ? $w_port : 3306;
        $this->connection_master = $this->db_connect($w_servername, $w_port, $w_username, $w_password, $configfile, $charset);
        if ($this->connection_master) {
            $this->select_db($this->database);
        }
    }

    /**
     * @param $servername
     * @param $port
     * @param $username
     * @param $password
     * @param string $configfile
     * @param string $charset
     * @return bool|mysqli
     */
    function db_connect($servername, $port, $username, $password, $configfile = '', $charset = '')
    {
        if (function_exists('catch_db_error')) {
            set_error_handler('catch_db_error');
        }

        $link = mysqli_init();
        if (!empty($configfile)) {
            mysqli_options($link, MYSQLI_READ_DEFAULT_FILE, $configfile);
        }

        $connect = $this->functions['connect']($link, $servername, $username, $password, '', $port);
        if ($connect == false) {
           echo 'DB error';
            exit;
        }
        restore_error_handler();

        if (!empty($charset)) {
            if (function_exists('mysqli_set_charset')) {
                mysqli_set_charset($link, $charset);
            } else {
                $this->sql = "SET NAMES $charset";
                $this->execute_query(true, $link);
            }
        }

        return (!$connect) ? false : $link;
    }

    /**
     * @param bool|true $buffered
     * @param $link
     * @return bool|mysqli_result|string
     */
    function &execute_query($buffered = true, &$link)
    {
        $this->connection_recent =& $link;
        $this->querycount++;
		if ($this->registry->config['system']['debug'] and preg_match('#SQL_NO_CACHE#Uis', $this->sql)==false ) {
			$this->sql = str_ireplace('SELECT ', 'SELECT SQL_NO_CACHE ',$this->sql);
		}
        if ($queryresult = mysqli_query($link, $this->sql, ($buffered ? MYSQLI_STORE_RESULT : MYSQLI_USE_RESULT))) {
            $this->sql = '';

            return $queryresult;
        } else {
            $this->halt();
            $this->sql = '';
            return '';
        }
    }

    /**
     * @param string $errortext
     */
    function halt($errortext = '')
    {
        if ($this->connection_recent) {
            $this->error = $this->error();
            $this->errno = $this->errno();
        }
        if ($this->log_errors) {
            $this->log_errors=false;
            if ($errortext == '') {
                $this->sql = "Invalid SQL:\r\n" . rtrim($this->sql) . ';';
                $errortext =& $this->sql;
            }

           $message = 'Database error:
            ' . $errortext . '
            MySQL Error   : ' . $this->error . '<br/>
            Error Number  : ' . $this->errno . '<br/>
            Classname     : ' . get_class($this).'<br/>
            Script        : ' .BASE_URL . str_replace('&amp;', '&', $this->registry->input->scriptpath);

            $error_data = array(
                'message' => $message,
                'priority' => 'high',
                'type'=>'db'
            );

            $this->registry->error->ShowError('database_error', 500, $error_data,true);
            exit;
        } else if (!empty($errortext)) {
            $this->error = $errortext;
        }
    }

    /**
     * @return string
     */
    function error()
    {
        if ($this->connection_recent === null) {
            $this->error = '';
        } else {
            $this->error = $this->functions['error']($this->connection_recent);
        }
        return $this->error;
    }

    /**
     * @return int|string
     */
    function errno()
    {
        if ($this->connection_recent === null) {
            $this->errno = 0;
        } else {
            $this->errno = $this->functions['errno']($this->connection_recent);
        }
        return $this->errno;
    }


    /**
     * @param string $database
     * @return bool
     */
    function select_db($database = '')
    {
        if ($database != '') {
            $this->database = $database;
        }

        if ($check_write = @$this->select_db_wrapper($this->database, $this->connection_master)) {
            $this->connection_recent =& $this->connection_master;
            return true;
        } else {
            $this->connection_recent =& $this->connection_master;
            $this->halt('Cannot use database ' . $this->database);
            return false;
        }
    }

    /**
     * @param string $database
     * @param null $link
     * @return mixed
     */
    function select_db_wrapper($database = '', $link = null)
    {
        return $this->functions['select_db']($link, $database);
    }

    /**
     * @param $sql
     * @param int $type
     * @return mixed
     */
    function &query_first($sql, $type = DBARRAY_ASSOC)
    {
        $this->sql =& $sql;
        $queryresult = $this->execute_query(true, $this->connection_master);
        $returnarray = $this->fetch_array($queryresult, $type);
        $this->free_result($queryresult);
        return $returnarray;
    }

    /**
     * @param $queryresult
     * @param int $type
     * @return mixed
     */
    function fetch_array($queryresult, $type = DBARRAY_ASSOC)
    {
        return @$this->functions['fetch_array']($queryresult, $this->fetchtypes["$type"]);
    }

    /**
     * @param $queryresult
     * @return mixed
     */
    function free_result($queryresult)
    {
        $this->sql = '';
        return @$this->functions['free_result']($queryresult);
    }

    /**
     * @param $mode
     */
    function force_sql_mode($mode)
    {
        $this->query_write("SET @@sql_mode = '" . $this->escape_string($mode) . "'");
    }

    /**
     * @param $sql
     * @param bool|true $buffered
     * @return bool|mysqli_result|string
     */
    function query_write($sql, $buffered = true)
    {
        $this->sql =& $sql;
        return $this->execute_query($buffered, $this->connection_master);
    }

    /**
     * @param $string
     * @return mixed
     */
    function escape_string($string)
    {
        return $this->functions['real_escape_string']($this->connection_master, $string);
    }

    /**
     * @param $sql
     * @param bool|true $buffered
     * @return bool|mysqli_result|string
     */
    function query_read($sql, $buffered = true)
    {
        $this->sql =& $sql;
        return $this->execute_query($buffered, $this->connection_master);
    }


    /**
     * @param $sql
     * @param bool|true $buffered
     * @return bool|mysqli_result|string
     */
    function query($sql, $buffered = true)
    {
        $this->sql =& $sql;
        return $this->execute_query($buffered, $this->connection_master);
    }


    /**
     * @return int
     */
    function found_rows()
    {
        $this->sql = "SELECT FOUND_ROWS()";
        $queryresult = $this->execute_query(true, $this->connection_recent);
        $returnarray = $this->fetch_array($queryresult, DBARRAY_NUM);
        $this->free_result($queryresult);

        return intval($returnarray[0]);
    }

    /**
     * @param $table
     * @param $fields
     * @param $values
     * @param bool|true $buffered
     * @return bool
     */
    function &query_insert($table, $fields, &$values, $buffered = true)
    {
        return $this->insert_multiple("INSERT INTO $table $fields VALUES", $values, $buffered);
    }


    /**
     * @param $sql
     * @param $values
     * @param $buffered
     * @return bool
     */
    function insert_multiple($sql, &$values, $buffered)
    {
        if ($this->maxpacket == 0) {
            $vars = $this->query_write("SHOW VARIABLES LIKE 'max_allowed_packet'");
            $var = $this->fetch_row($vars);
            $this->maxpacket = $var[1];
            $this->free_result($vars);
        }

        $i = 0;
        $num_values = sizeof($values);
        $this->sql = $sql;

        while ($i < $num_values) {
            $sql_length = strlen($this->sql);
            $value_length = strlen("\r\n" . $values["$i"] . ",");

            if (($sql_length + $value_length) < $this->maxpacket) {
                $this->sql .= "\r\n" . $values["$i"] . ",";
                unset($values["$i"]);
                $i++;
            } else {
                $this->sql = (substr($this->sql, -1) == ',') ? substr($this->sql, 0, -1) : $this->sql;
                $this->execute_query($buffered, $this->connection_master);
                $this->sql = $sql;
            }
        }
        if ($this->sql != $sql) {
            $this->sql = (substr($this->sql, -1) == ',') ? substr($this->sql, 0, -1) : $this->sql;
            $this->execute_query($buffered, $this->connection_master);
        }

        if (sizeof($values) == 1) {
            return $this->insert_id();
        } else {
            return true;
        }
    }


    /**
     * @param $queryresult
     * @return mixed
     */
    function fetch_row($queryresult)
    {
        return @$this->functions['fetch_row']($queryresult);
    }


    /**
     * @return mixed
     */
    function insert_id()
    {
        return @$this->functions['insert_id']($this->connection_master);
    }


    /**
     * @param $table
     * @param $fields
     * @param $values
     * @param bool|true $buffered
     * @return bool
     */
    function &query_replace($table, $fields, &$values, $buffered = true)
    {
        return $this->insert_multiple("REPLACE INTO $table $fields VALUES", $values, $buffered);
    }

    /**
     * @param $sql
     * @param int $arraykey
     * @return bool
     */
    function shutdown_query($sql, $arraykey = -1)
    {
        if ($arraykey === -1) {
            $this->shutdownqueries[] = $sql;
            return true;
        } else {
            $this->shutdownqueries["$arraykey"] = $sql;
            return true;
        }
    }

    /**
     * @param $queryresult
     * @return mixed
     */
    function num_rows($queryresult)
    {
        return @$this->functions['num_rows']($queryresult);
    }

    /**
     * @param $queryresult
     * @return mixed
     */
    function num_fields($queryresult)
    {
        return @$this->functions['num_fields']($queryresult);
    }


    /**
     * @param $queryresult
     * @return mixed
     */
    function field_name($queryresult)
    {
        $field = @$this->functions['fetch_field']($queryresult);
        return $field->name;
    }


    /**
     * @return mixed
     */
    function client_encoding()
    {
        return @$this->functions['client_encoding']($this->connection_master);
    }

    /**
     * @return mixed
     */
    function close()
    {
        return @$this->functions['close']($this->connection_master);
    }


    /**
     * @param $string
     * @return mixed
     */
    function escape_string_like($string)
    {
        return str_replace(array('%', '_'), array('\%', '\_'), $this->escape_string($string));
    }


    /**
     * @param $value
     * @return int|string
     */
    function sql_prepare($value)
    {
        if (is_string($value)) {
            return "'" . $this->escape_string($value) . "'";
        } else if (is_numeric($value) AND $value + 0 == $value) {
            return $value;
        } else if (is_bool($value)) {
            return $value ? 1 : 0;
        } else {
            return "'" . $this->escape_string($value) . "'";
        }
    }


    /**
     * @param $queryresult
     * @return mixed
     */
    function fetch_field($queryresult)
    {
        return @$this->functions['fetch_field']($queryresult);
    }


    /**
     * @param $queryresult
     * @param $index
     * @return mixed
     */
    function data_seek($queryresult, $index)
    {
        return @$this->functions['data_seek']($queryresult, $index);
    }

    /**
     * @return mixed
     */
    function affected_rows()
    {
        $this->rows = $this->functions['affected_rows']($this->connection_recent);
        return $this->rows;
    }

    /**
     * @param $tablelist
     */
    function lock_tables($tablelist)
    {
        if (!empty($tablelist) AND is_array($tablelist)) {
            $sql = '';
            foreach ($tablelist AS $name => $type) {
                $sql .= (!empty($sql) ? ', ' : '') . $name . " " . $type;
            }

            $this->query_write("LOCK TABLES $sql");
            $this->locked = true;

        }
    }

    /**
     *
     */
    function unlock_tables()
    {
        if ($this->locked) {
            $this->query_write("UNLOCK TABLES");
        }
    }

    /**
     * @return mixed
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param mixed $registry
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }


    function getThreadId() {
	    return mysqli_thread_id($this->connection_master);
    }

	function kill($thread_id) {
		return mysqli_kill($this->connection_master, $thread_id);
	}
}

