<?php
$config = array();

$config['Database']['dbname'] = 'tans';

$config['Database']['technicalemail'] = 'support@trans.ru';

$config['MasterServer']['servername'] = 'localhost';

$config['MasterServer']['username'] = 'root';
$config['MasterServer']['password'] = 'root';



$config['MasterServer']['port'] = 3306;
$config['Mysqli']['charset'] = 'utf8';

$config['system']['default_timezone'] = "Europe/Moscow";
$config['system']['default_locale'] = array('ru_RU.UTF-8','rus');
$config['system']['debug'] =false;

$config['system']['admin_email']='arif.settarov@mail.ru';
$config['system']['confirm_timeout']=15; //minutes
$config['system']['change_email_timeout']=15; //minutes
$config['system']['confirm_route']='confirm';
$config['system']['change_email_route']='change_email';
$config['system']['forgot_route']='forgot';
$config['system']['forgot_process_route']='recover';
$config['system']['register_route']='register';
$config['system']['login_route']='login';
$config['system']['logout_route']='logout';
$config['system']['new_email_route']='change_email';
$config['system']['profile_route']='account';
$config['system']['email_name']='TransUgCrimea';
$config['system']['email_addr']='tigereng@yandex.ru';
$config['system']['forgot_timeout']=15; //minutes
$config['system']['change_email_time']=900;
$config["user"]["cookie_timeout"]=24*3600;
$config["user"]["max_strikes"]=5;
$config["user"]["strike_time"]=15;
$config["user"]["persistent_session"]=true;
$config["images"]["path"]=ROOT_DIR.'/uploads/images/';
$config["images"]["url"]=BASE_URL.'/uploads/images/';

$config["email"]["smtp"]["server"]='smtp.yandex.ru';
$config["email"]["smtp"]["port"]=465;
$config["email"]["smtp"]["ssl"]='ssl';
$config["email"]["smtp"]["login"]='tigereng@yandex.ru';
$config["email"]["smtp"]["password"]='Ctnnfhjdfhba1998';
