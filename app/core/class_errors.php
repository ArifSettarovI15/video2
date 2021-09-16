<?php

class ErrorClass
{
	/**
	 * @var MainClass
	 */
	var $registry;

	/**
	 * @var DatabaseClass
	 */
	var $db;

	var $path='error';

	function __construct($registry)
	{
		$this->registry =& $registry;
		$this->db =& $this->registry->db;
	}

	function header_status($statusCode) {
		static $status_codes = null;

		if ($status_codes === null) {
			$status_codes = array (
				100 => 'Continue',
				101 => 'Switching Protocols',
				102 => 'Processing',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				207 => 'Multi-Status',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				422 => 'Unprocessable Entity',
				423 => 'Locked',
				424 => 'Failed Dependency',
				426 => 'Upgrade Required',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported',
				506 => 'Variant Also Negotiates',
				507 => 'Insufficient Storage',
				509 => 'Bandwidth Limit Exceeded',
				510 => 'Not Extended'
			);
		}

		if ($status_codes[$statusCode] !== null) {
			$status_string = $statusCode . ' ' . $status_codes[$statusCode];
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
		}
	}

	function PageNotFound() {
		if (defined('BASE_URL_RESERV') && BASE_URL!=BASE_URL_RESERV && preg_match_all('#uploads#Us',  $_SERVER['REQUEST_URI'], $matches)) {
			$url=BASE_URL_RESERV.$this->registry->input->scriptpath;
			Redirect_to($url,301);
		}
		$this->ShowError('page_not_found',404);
	}
	function ObjectNotFound() {
		$this->ShowError('object_not_exist',404);
	}
	function EmptyResult() {
		return $this->ShowError('empty_result',404,array(),false,true);
	}
	function AccessDenied() {
		$this->ShowError('access_denied',401);
	}

	function InsertError ($title,$message,$type,$priority,$error_code,$page_url,$referer,$ip,$user_agent,$user_id) {
		$this->registry->db->query_write("INSERT INTO `core_log_errors`
        (`priority`,`type`,`title`,`errortext`,`error_code`,`page_url`,`referer`,`ip`,`user_agent`,`user_id`)
        VALUES (
        ".$this->registry->db->sql_prepare($priority).",
        ".$this->registry->db->sql_prepare($type).",
        ".$this->registry->db->sql_prepare($title).",
        ".$this->registry->db->sql_prepare($message).",
        ".$this->registry->db->sql_prepare($error_code).",
        ".$this->registry->db->sql_prepare($page_url).",
        ".$this->registry->db->sql_prepare($referer).",
        ".$this->registry->db->sql_prepare($ip).",
        ".$this->registry->db->sql_prepare($user_agent).",
        ".$this->registry->db->sql_prepare($user_id)."
        )");
	}


	function LogError ($data) {
		if ($data['priority']=='high') {
			$this->InsertError(
				$data['title'],
				$data['message'],
				$data['type'],
				$data['priority'],
				$data['error_code'],
				BASE_URL.str_replace('&amp;', '&', $this->registry->input->scriptpath),
				REFERRER,
				$this->registry->input->ipaddress,
				$_SERVER['HTTP_USER_AGENT'],
				intval($this->registry->user_info['user_id'])
			);

			$this->SendErrorEmail(
				$data['title'],
				$data['message'],
				array($this->registry->config['Database']['technicalemail']),
				array($this->registry->config['Database']['technicalemail'])
			);
		}
	}

	function SendErrorEmail ($title, $content, $from_array, $to_array, $type='text/html'){
		$message = (new Swift_Message($title))
			->setFrom($from_array)
			->setTo($to_array)
			->setBody($content, $type)
		;

		$result=false;
		try{
			$result = $this->registry->system_mailer->send($message);
		}catch(\Swift_TransportException $e){
			$response = $e->getMessage() ;
		}
		return $result;
	}


	function InlineError ($error_code,$statusCode= 200,$error_info=array(),$simple=false){
		return $this->ShowError ($error_code,$statusCode,$error_info,$simple,true);
	}
	function ShowError ($error_code,$statusCode= 200,$error_info=array(),$simple=false,$inline=false){
		$error=$this->registry->lang->data['error_codes'][$error_code];
		if ($error=='') {
			$error=$error_code;
		}
		$error_array=array(
			'error_code'=>$error_code,
			'priority'=>'normal',
			'type'=>'general',
			'title'=>$error
		);
		if ($error_info['priority']) {
			$error_array['priority']=$error_info['priority'];
		}
		if ($error_info['priority']) {
			$error_array['type']=$error_info['type'];
		}
		$error_array['message']=$error_info['message'];

		$this->LogError($error_array);

		if ($this->registry->GPC['ajax']) {
			$json=array();
			$json['status']=false;
			$json['text']=$error;
			$this->registry->template->DisplayJson($json);
		}
		else {
			if ($inline==false) {
				$this->header_status($statusCode);
			}


			if ($simple == false) {
				if ($inline==false) {
					if ($statusCode==404) {
						$this->registry->template->SetPageAttributes(
							array(
								'title'=>'Страница не найдена',
								'keywords'=>'',
								'desc'=>''
							)
						);
						$this->registry->template->global_vars['error404']=true;
						$this->registry->template->DisplayCore(
							'static/404.twig'
						);
					}
					else {
						$this->registry->template->SetPageAttributes(
							array(
								'title'=>$this->registry->lang->data['error']['page_name'],
								'keywords'=>'',
								'desc'=>''
							),
							array(
								'breadcrumbs'=>array(
									array(
										'title'=>$this->registry->lang->data['error']['page_name']
									)
								),
								'title'=>$this->registry->lang->data['error']['page_name']
							)
						);
						$this->registry->template->DisplayCore(
							'static/error.html.twig',
							array(
								'message_head' => $this->registry->lang->data['error']['page_name'],
								'message' => $error,
								'message_class' => 'error'
							)
						);
					}

				}
				else {
					return  $this->registry->template->Render(
						'global/message.html.twig',
						array(
							'message' => $error,
							'message_class' => 'error'
						)
					);
				}
			} else {
				echo $error;
			}
		}
		exit;
	}
}
