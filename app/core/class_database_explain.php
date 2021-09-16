<?php
if (!class_exists('DatabaseClass')) {
    exit;
}

class DatabaseClass_Explain extends DatabaseClass
{
    var $message = '';
    var $prev_messages = '';

    var $message_title = array();

    var $memory_before = array();

    var $time_before = array();

    var $time_total = 0;

    var $explain = true;

    function db_connect($servername, $port, $username, $password, $configfile = '', $charset = '')
    {
        $this->timer_start("Connect to Database on Server: <em>$servername</em>");
        $return = parent::db_connect($servername, $port, $username, $password, $configfile, $charset);
        $this->timer_stop();
        return $return;
    }

    function timer_start($str = '')
    {
        $this->message_title[] = $str;

        if (function_exists('memory_get_usage')) {
            $this->memory_before[] = memory_get_usage();
        }
        $this->time_before[] = microtime();
    }

    function timer_stop($add_total = true)
    {
        $time_after = microtime();

        $pagestart = explode(' ', TIMESTART);
        $pagestart = $pagestart[0] + $pagestart[1];

        $time_before = explode(' ', array_pop($this->time_before));
        $time_before = $time_before[0] + $time_before[1] - $pagestart;

        $time_after = explode(' ', $time_after);
        $time_after = $time_after[0] + $time_after[1] - $pagestart;

        $time_taken = $time_after - $time_before;

        if ($add_total) {
            $this->time_total += $time_taken;
        }

        $this->output("<p>Time Before: " . number_format($time_before, 5) . " seconds<br />");
        $this->output("Time After: " . number_format($time_after, 5) . " seconds<br />");
        $this->output("Time Taken: " . number_format($time_taken, 5) . " seconds</p>");

        if (function_exists('memory_get_usage')) {
            $memory_before = array_pop($this->memory_before);
            $memory_after = memory_get_usage();

            $this->output("<p>Memory Before: " . number_format($memory_before / 1024, 3) . " KB<br />");
            $this->output("Memory After: " . number_format($memory_after / 1024, 3) . " KB<br />");
            $this->output("Memory Used: " . number_format(($memory_after - $memory_before) / 1024, 3) . " KB</p>");
        }
        if (sizeof($this->message_title) == 1) {
            $this->message = $this->prev_messages . "\n" . $this->message;
        }

        $output = "<div class=\"explain\">
			<div class=\"explaintitle\">" . array_pop($this->message_title) . "</div>
			<div class=\"explainbody\">$this->message</div>
		</div>";

        if (sizeof($this->message_title) == 0) {
            echo $output;
            $this->prev_messages = '';
        } else {
            $this->prev_messages .= $output;
        }
        $this->message = '';
    }

    function output($str)
    {
        static $doneheader = false;

        if (!$doneheader) {
            $doneheader = true;
            echo '<html>
			<head>
				<title>TW</title>
				<style type="text/css">
				<!--
				body { color: black; background-color: #DDDDDD; }
				body, p, td, th { font-family: verdana, sans-serif; font-size: 10pt; text-align: left; }
				pre { padding: 8px; border: 1px solid; background: #F6F6F6; }
				table { border: 1px solid black; border-collapse: collapse; }
				th { background: #F6F6F6; border: 1px solid black; }
				td { border: 1px solid black; }
				div.explain { z-index:100; border: 1px solid red; margin-bottom: 16px; }
				div.explaintitle { color: black; background-color: white; padding: 4px; font-weight: bold; border-bottom: 1px solid red; }
				div.explainbody { padding: 8px; color: black; background-color: white; }
				-->
				</style>
			</head>
			<body>';
        }

        $this->message .= $str;
    }

    function &execute_query($buffered = true, &$link)
    {
	    if ($this->registry->config['system']['debug'] and preg_match('#SQL_NO_CACHE#Uis', $this->sql)==false) {
		    $this->sql = str_ireplace('SELECT ', 'SELECT SQL_NO_CACHE ',$this->sql);
	    }
        if (preg_match('#^\s*SELECT\s#s', $this->sql)) {
            $this->explain_query($link);
        } else {
            $this->output("<pre>" . trim((preg_match('#(\n\s+)(UPDATE|INSERT|REPLACE)\s#s', $this->sql, $match) ? str_replace($match[1], "\n", $this->sql) : $this->sql)) . "</pre>");
        }

        $this->timer_start('SQL Query');
        $return = parent::execute_query($buffered, $link);
        $this->timer_stop();
        return $return;
    }

    function explain_query(&$link)
    {
        $this->output("<pre>" . trim((preg_match('#(\n\s+)SELECT\s#s', $this->sql, $match) ? str_replace($match[1], "\n", $this->sql) : $this->sql)) . "</pre>");

        $results = $this->functions['query']($link, 'EXPLAIN ' . $this->sql);
        $this->output('<table width="98%" cellpadding="2" cellspacing="1"><tr>');
        while ($field = $this->functions['fetch_field']($results)) {
            $this->output('<th>' . $field->name . '</th>');
        }
        $this->output('</tr>');
        $numfields = $this->functions['num_fields']($results);
        while ($result = $this->fetch_row($results)) {
            $this->output('<tr>');
            for ($i = 0; $i < $numfields; $i++) {
                $this->output("<td>" . ($result["$i"] == '' ? '&nbsp;' : $result["$i"]) . "</td>");
            }
            $this->output('</tr>');
        }
        $this->output('</table>');
    }
}
