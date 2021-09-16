<?php

class UserClass
{
    /**
     * @var MainClass
     */
    var $registry;
    /**
     * @var string
     */
    var $user_hash;
    /**
     * @var DatabaseClass
     */
    var $db;

    /**
     * @var array
     */
    var $lang;

    /**
     * @var array
     */
    var $lang_js;

    /**
     * @var array
     */
    var $options=array();

    public function __construct(&$registry)
    {

        $this->registry =& $registry;
        $this->db =& $this->registry->db;
        $this->options['cookie_timeout']=$this->registry->config["user"]["cookie_timeout"];
        $this->options['confirm_timeout']=$this->registry->config['system']['confirm_timeout'];
        $this->options['change_email_timeout']=$this->registry->config['system']['change_email_timeout'];
        $this->options['forgot_timeout']=$this->registry->config['system']['forgot_timeout'];
        $this->options['change_email_route']=$this->registry->config['system']['change_email_route'];
        $this->options['confirm_route']=$this->registry->config['system']['confirm_route'];
        $this->options['email_name']=$this->registry->config['system']['email_name'];
        $this->options['email_addr']=$this->registry->config['system']['email_addr'];
        $this->options['forgot_route']=$this->registry->config['system']['forgot_route'];
        $this->options['forgot_process_route']=$this->registry->config['system']['forgot_process_route'];
        $this->options['login_route']=$this->registry->config["system"]["login_route"];
        $this->options['logout_route']=$this->registry->config["system"]["logout_route"];
        $this->options['register_route']=$this->registry->config["system"]["register_route"];
        $this->options['new_email_route']=$this->registry->config["system"]["new_email_route"];
        $this->options['profile_route']=$this->registry->config["system"]["profile_route"];
        $this->user_hash= $this->MakeUserHash();
        $this->lang=$this->registry->lang->data['users'];
        $this->options['strike_time']=$this->registry->config["user"]["strike_time"];
        $this->options['max_strikes']=$this->registry->config["user"]["max_strikes"];
        $this->options['persistent_session']=$this->registry->config["user"]["persistent_session"];

        $this->registry->settings_values['links']['logout']=$this->LogoutPageLink();
        $this->registry->settings_values['links']['register']=$this->RegisterPageLink();
        $this->registry->settings_values['links']['forgot']=$this->ForgotPageLink();
        $this->registry->settings_values['links']['login']=$this->LoginPageLink();
        $this->registry->settings_values['links']['new_email']=$this->NewEmailPageLink();
        $this->registry->settings_values['links']['confirm']=$this->ConfirmPageLink();
        $this->registry->settings_values['links']['profile']=$this->ProfilePageLink();


    }

    /**
     * @param $ip string
     * @param $length integer|null
     * @return string
     */
    function fetch_substring_ip($ip, $length = null)
    {
        if ($length === null OR $length > 3  OR $length <1) {
            $length = 1;
        }
        return implode('.', array_slice(explode('.', $ip), 0, 4 - $length));
    }

    /**
     * Unique hash of params: browser, subnet ip
     * @return string
     */
    function MakeUserHash () {
        return md5(
            $_SERVER['HTTP_USER_AGENT'].
            $this->fetch_substring_ip($this->registry->input->alt_ip,3)
        );
    }


    /**
     * @param $sid string
     * @param $uid string
     * @return array|mixed
     */
    function AuthUser ($sid, $uid){
        $user_info=array(
            'user_id'=>0,
            'sessionhash'=>'',
            'role_id'=>0
        );
        $user = $this->GetUserBySession($sid,$uid);

        if ($user) {
            if (intval($user["user_id"])>0) {
                $user_info= $this->GetUserById($user["user_id"]);
            }
            if ($this->options['persistent_session']==false) {
                $this->UpdateUserActivity($sid);
            }
            $user_info['sessionhash']=$sid;
            unset($user_info['user_password']);
            unset($user_info['user_salt']);
        }
        else {
            $user_info['sessionhash']=$this->CreateUserSession();
        }

        return $user_info;
    }

    /**
     * @param $sid string
     * @return mixed
     */
    function SelectUserSession ($sid) {
        return $this->db->query_first("SELECT * 
        FROM `users_session`
        WHERE `sessionhash`=".$this->db->sql_prepare($sid));
    }


    function DeleteOldSession ($time) {
        return $this->db->query_write("DELETE FROM `users_session`
        WHERE `user_id`=0 and last_activity<".intval($time));
    }

    function userNameExplode($string){
        if (strpos($string," ")){
        $data=explode(" ", $string);
        $index=0;
        foreach($data as $k=>$v){
            if ($v=='') {
                unset($data[$k]);
            }
            else{
                $data[$index]=$v;
                unset($data[$k]);
                $index+=1;
            }
        }
        }
        else{
            $data = $string;
        }

        return $data;
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $sid string
     * @param $uid string
     * @return bool|mixed
     */

    function GetUserBySession ($sid, $uid) {
        if ($sid!='' && $uid!='') {
            $UserSession = $this->SelectUserSession($sid);
            if ($UserSession) {
                if (
                    $UserSession['sessionhash'] != '' &&
                    $UserSession['user_hash'] == $this->user_hash &&
                    md5($UserSession['user_id']) == $uid &&
                    (
                        $UserSession['last_activity'] > (TIMENOW - $this->options['cookie_timeout']*60)
                         OR
                        $this->options['persistent_session']==true
                    )
                    &&
                    $this->fetch_substring_ip($UserSession['host']) == $this->fetch_substring_ip(SESSION_HOST)
                ) {
                    return $UserSession;
                }
            }
        }
        return false;
    }

    function UpdateUserActivity ($session_hash) {
        $this->db->query_write('UPDATE `users_session`
        SET `last_activity`='.$this->db->sql_prepare(TIMENOW).'
        WHERE `sessionhash`='.$this->db->sql_prepare($session_hash));
    }

    /**
     * @param $user_id int
     * @return mixed
     */
    function GetUserById ($user_id) {
        $result=$this->SelectUserById($user_id);
        return $result;
    }

    /**
     * @param $user_id int
     * @return mixed
     */
    function SelectUserById ($user_id) {
        return $this->db->query_first("SELECT * 
        FROM `users` 
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @return string
     */
    function fetch_sessionhash()
    {
        return md5(uniqid(microtime(), true));
    }

    /**
     * @param $sessionhash string
     * @param $user_hash string
     * @param $user_id int
     * @param $last_activity int
     * @param $host string
     * @return bool|mysqli_result|string
     */
    function InsertUserSession ($sessionhash, $user_hash, $user_id, $last_activity, $host) {
        return $this->db->query_write("INSERT INTO `users_session` 
      (
      `sessionhash`,
      `user_hash`,
      `user_id`,
      `last_activity`,
      `host`
      )
        VALUES (
        ".$this->db->sql_prepare($sessionhash).",
        ".$this->db->sql_prepare($user_hash).",
        ".$this->db->sql_prepare($user_id).",
        ".$this->db->sql_prepare($last_activity).",
        ".$this->db->sql_prepare($host)."
        )");
    }

    /**
     * @param $sessionhash string
     * @param $user_id int
     * @return bool|mysqli_result|string
     */
    function UpdateUserIdSession ($sessionhash, $user_id) {
        return $this->db->query_write("UPDATE `users_session` 
        SET  `user_id`=".$this->db->sql_prepare($user_id)."
        WHERE `sessionhash`=".$this->db->sql_prepare($sessionhash));
    }
    /**
     * @param $session_hash string
     * @param $uid string
     * @param $expire mixed
     */
    function SetUserCookie ($session_hash, $uid,$expire=false){
        Set_Cookie('sid',$session_hash,$expire);
        Set_Cookie('uid',$uid,$expire);
    }

    /**
     * @param int $user_id
     * @return string
     */
    function CreateUserSession ($user_id=0) {
        $sessionhash=$this->fetch_sessionhash();
        $user_hash=$this->user_hash;
        $last_activity=TIMENOW;
        $host=SESSION_HOST;
        $this->InsertUserSession($sessionhash,$user_hash,$user_id,$last_activity,$host);
        $uid=md5($user_id);
        $this->SetUserCookie($sessionhash,$uid);
        return $sessionhash;
    }

    /**
     * @param string $sessionhash
     * @param int $user_id
     * @param mixed $expire
     * @return string
     */
    function UpdateUserSession ($sessionhash,$user_id=0,$expire=false) {
        $this->UpdateUserIdSession($sessionhash,$user_id);
        $uid=md5($user_id);
        $this->SetUserCookie($sessionhash,$uid,$expire);
        return $sessionhash;
    }

    /**
     * @param $login string
     * @param bool $secure
     * @return mixed
     */
    function GetUserByLogin ($login, $secure=true) {
        $user_info=$this->SelectUserByLogin($login);
        if ($user_info && $secure==true) {
            unset($user_info['user_password']);
            unset($user_info['user_salt']);
        }
        return $user_info;
    }

    /**
     * @param $login string
     * @param $email string
     * @return bool
     */
    function CheckUserLoginAndEmail ($login, $email) {
        $user_info=$this->GetUserByLogin($login);
        if ($user_info && strtolower($email)==strtolower($user_info['user_email'])) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param $login string
     * @return mixed
     */
    function SelectUserByLogin ($login) {
        $user_info=$this->db->query_first("SELECT * FROM `users` WHERE `user_login`=".$this->db->sql_prepare($login));
        return $user_info;
    }
    /**
     * @param $login string
     * @param $current_user int
     * @return bool
     */
    function CheckUserLogin ($login,$current_user=0) {
        $user_info=$this->GetUserByLogin($login);
        if ($user_info['user_id'] && $current_user!=$user_info['user_id']) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param $user_id int
     * @param $login string
     * @param $password string
     * @param $password_confirm string
     * @param $email string
     * @return string
     */
    function CheckRegisterFields ($user_id,$login, $password, $password_confirm, $email) {
        $error='';
        $field='';
        /*if ($login=='') {
            $error=$this->lang['enter_login'];
            $field='login';
        }
        else*/
        if ($email=='') {
		    $error=$this->lang['enter_email'];
		    $field='email';
	    }
	    elseif (is_valid_email($email)==false) {
		    $error=$this->lang['enter_correct_email'];
		    $field='email';
	    }
        /*elseif ($this->CheckUserLogin($login)) {
            $error=$this->lang['login_exist'];
            $field='login';
        }*/
        elseif ($this->CheckUserLogin($login,$user_id)) {
            $error=$this->lang['email_exist'];
            $field='email';
        }
        elseif ($password=='' && $user_id==0) {
            $error=$this->lang['enter_password'];
            $field='password';
        }
        elseif ($password_confirm=='' && $user_id==0) {
            $error=$this->lang['enter_password2'];
            $field='password2';
        }
        elseif ($password_confirm!=$password) {
            $error=$this->lang['password_different'];
            $field='password,password2';
        }
        return array($error,$field);
    }

    function CheckProfileFields ($data) {
        $error='';
        $field='';

        return array($error,$field);
    }
    /**
     * @param $password string
     * @param $salt string
     * @return string
     */
    function CreateDbPassword ($password, $salt){
    	return sha1($salt. sha1($salt. sha1($password)));
    }

    /**
     * @param $login string
     * @param $password_db string
     * @param $salt string
     * @param $email string
     * @param $active int
     * @param $time int
     * @param $role_id int
     * @return bool|mixed
     */
    function InsertUser($login, $password_db, $salt, $email, $active,$time, $role_id)
    {
        $status=$this->db->query_write("INSERT INTO `users` (`user_login`,`user_password`,`user_salt`,
        `user_email`,`user_active`,`user_regtime`,`user_role_id`)
        VALUES (".$this->db->sql_prepare($login).",
        ".$this->db->sql_prepare($password_db).",
         ".$this->db->sql_prepare($salt).",
        ".$this->db->sql_prepare($email).",
        ".$this->db->sql_prepare($active).",
        ".$this->db->sql_prepare($time).",
        ".$this->db->sql_prepare($role_id).")");
        if ($status) {
            return $this->db->insert_id();
        }
        else {
            return false;
        }
    }
//    function InsertUser($login, $code, $active,$time, $role_id,$salt="NgX=?454", $email="user@mail.ru")
//    {
//        $status=$this->db->query_write("INSERT INTO `users` (`user_login`,`user_password`,`user_salt`,
//        `user_email`,`user_active`,`user_regtime`,`user_role_id`)
//        VALUES (".$this->db->sql_prepare($login).",
//        ".$this->db->sql_prepare($code).",
//         ".$this->db->sql_prepare($salt).",
//        ".$this->db->sql_prepare($email).",
//        ".$this->db->sql_prepare($active).",
//        ".$this->db->sql_prepare($time).",
//        ".$this->db->sql_prepare($role_id).")");
//        if ($status) {
//            return $this->db->insert_id();
//        }
//        else {
//            return false;
//        }
//    }

    /**
     * @param $login string
     * @param $password string
     * @param $email string
     * @param $active int
     * @param $role_id int
     * @return bool|mixed
     */
    function CreateUser($login, $password, $email,$active, $role_id)
    {
        $salt=GenerateName(10,4);
        $password_db=$this->CreateDbPassword($password,$salt);
        $user_id=$this->InsertUser($login,$password_db,$salt,$email,$active,TIMENOW,$role_id);
        return $user_id;
    }
//function CreateUser($login, $code, $active, $role_id)
//    {
////        $salt=GenerateName(10,4);
//
//        $user_id=$this->InsertUser($login,$code,$active,TIMENOW,$role_id,$salt="NgX=?454", $email="user@mail.ru");
//        return $user_id;
//    }

    /**
     * @param $user_id int
     * @param $login string
     * @param $email string
     * @param $active bool
     * @param $role_id int
     * @return bool|mysqli_result|string
     */
    function UpdateUser($user_id, $login, $email, $active, $role_id)
    {
        return $this->UpdateUserDb($user_id,$login,$email,$active,$role_id);
    }

    /**
     * @param $user_id int
     * @param $login string
     * @param $email string
     * @param $active bool
     * @param $role_id int
     * @return bool|mysqli_result|string
     */
    function UpdateUserDb ($user_id, $login, $email, $active, $role_id){
        return $this->db->query_write("UPDATE `users` SET
        `user_login`=".$this->db->sql_prepare($login).",
        `user_email`=".$this->db->sql_prepare($email).",
        `user_active`=".$this->db->sql_prepare($active).",
        `user_role_id`=".$this->db->sql_prepare($role_id)."
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $type string
     * @param $user_id int
     */
    function DeleteHashesByUserId ($type, $user_id) {
        $this->db->query_write("DELETE FROM `users_hashes` 
        WHERE `type`=".$this->db->sql_prepare($type)." AND `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $user_id int
     */
    function DeleteForgotHash ($user_id){
        $this->DeleteHashesByUserId ('forgot',$user_id);
    }

    /**
     * @param $user_id int
     */
    function DeleteConfirmHashes ($user_id){
        $this->DeleteHashesByUserId ('confirm',$user_id);
    }

    /**
     * @param $user_id int
     */
    function DeleteChangeEmailHashes ($user_id){
        $this->DeleteHashesByUserId ('change_email',$user_id);
    }

    /**
     * @param $hash string
     * @param $user_id int
     * @param $time int
     * @param $type string
     * @param $data string
     * @return bool|mysqli_result|string
     */
    function InsertUserHash ($hash, $user_id, $time, $type,$data=''){
        return $this->db->query_write("INSERT INTO `users_hashes`
        (`hash`,`user_id`,`time`,`type`,`data`)
         VALUES (
          ".$this->db->sql_prepare($hash).",
          ".$this->db->sql_prepare($user_id).",
          ".$this->db->sql_prepare($time).",
          ".$this->db->sql_prepare($type).",
          ".$this->db->sql_prepare($data)."
         )");
    }

    /**
     * @param $user_id int
     * @param $hash string
     * @return bool|mysqli_result|string
     */
    function InsertConfirmHash ($user_id, $hash){
        $time=TIMENOW+$this->options['confirm_timeout']*60;
        return  $this->InsertUserHash ($hash,$user_id,$time,'confirm');
    }

    /**
     * @param $user_id int
     * @param $hash string
     * @param $data string
     * @return bool|mysqli_result|string
     */
    function InsertChangeEmailHash ($user_id, $hash,$data=''){
        $time=TIMENOW+$this->options['change_email_timeout']*60;
        return  $this->InsertUserHash ($hash,$user_id,$time,'change_email',$data);
    }
    /**
     * @param $user_id int
     * @param $hash string
     * @return bool|mysqli_result|string
     */
    function InsertForgotHash ($user_id, $hash){
        $time=TIMENOW+$this->options['forgot_timeout']*60;
        return  $this->InsertUserHash ($hash,$user_id,$time,'forgot');
    }

    /**
     * @param $confirm_hash string
     * @return string
     */
    function CreateConfirmLink ($confirm_hash) {
        $url=BASE_URL.'/'.$this->options['confirm_route'].'/'.$confirm_hash.'/';
        return $url;
    }
    /**
     * @param $hash string
     * @return string
     */
    function CreateChangeEmailLink ($hash) {
        $url=BASE_URL.'/'.$this->options['change_email_route'].'/'.$hash.'/';
        return $url;
    }
    /**
     * @param $title string
     * @param $content string
     * @param $from_array array
     * @param $to_array
     * @param $type string
     * @return int
     */
    function SendUserMail ($title, $content, $from_array, $to_array, $type='text/html'){
	    $message = (new Swift_Message($title))
		    ->setFrom($from_array)
		    ->setTo($to_array)
		    ->setBody($content, $type)
	    ;
	    try{
		    $result = $this->registry->mailer->send($message);
	    }catch(\Swift_TransportException $e){
		    $response = $e->getMessage() ;
	    }
        return $result;
    }

    /**
     * @param $user_id int
     * @return bool
     */
    function ProcessConfirmAccount ($user_id) {
        $this->DeleteConfirmHashes($user_id);

        $user_info=$this->GetUserById($user_id);
        $confirm_hash=fetch_random_string();
        $status=$this->InsertConfirmHash($user_id,$confirm_hash);
        if ($status) {
            $confirm_link = $this->CreateConfirmLink($confirm_hash);
            $title = $this->lang['account_confirm_email_title'];
            $from_array = array($this->options['email_addr']=>$this->options['email_name']);
            $to_array = array($user_info['user_email'] => $user_info['user_login']);
            $body = $this->registry->template->Render('users/emails/confirm_account.html.twig',
                array(
                    'title' => $this->lang['account_confirm_email_title'],
                    'confirm_link' => $confirm_link
                )
            );

            if ($this->SendUserMail($title, $body, $from_array, $to_array)) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    function ProcessChangeEmail ($user_id,$new_email){
        $user_info=$this->GetUserById($user_id);
        list($error,$error_field)=$this->CheckEmailField($new_email);
        if ($error=='') {

                if ($new_email == $user_info['user_email']) {
                    $error = 'Введенный Email совпадает с текущим';
                    $error_field = 'email';
                } else {
                    $data = $this->GetUserByLogin($new_email);
                    if ($data && ($data['user_id']!=$user_info['user_id'])) {
                        $error = 'Пользователь с таким Email уже зарегистрирован';
                        $error_field = 'email';
                    }
                }

            if ($error=='') {
                $this->DeleteChangeEmailHashes($user_id);
                $change_email_hash=fetch_random_string();
                $status=$this->InsertChangeEmailHash($user_id,$change_email_hash,$new_email);
                if ($status) {
                    $change_email_link = $this->CreateChangeEmailLink($change_email_hash);
                    $title = $this->lang['account_change_email_title'];
                    $from_array = array($this->options['email_addr']=>$this->options['email_name']);
                    $to_array = array($user_info['user_email']);
                    $body = $this->registry->template->Render('users/emails/change_email_account.html.twig',
                        array(
                            'title' => $this->lang['account_change_email_title'],
                            'change_email_link' => $change_email_link
                        )
                    );

                    if ($this->SendUserMail($title, $body, $from_array, $to_array)) {

                    }
                    else {
                        $error='Ошибка отправки Email';
                    }
                }
                else {
                    $error='Ошибка';
                }
            }
        }
        return array($error,$error_field);
    }

    /**
     * @param $user_id int
     * @param $login string
     * @param $password string
     * @param $password_confirm string
     * @param $email string
     * @param array $profile_data
     * @param bool $confirm_account
     * @param int $active
     * @param int $role_id
     * @return array
     */
    function SaveUser ($user_id,$login, $password, $password_confirm, $email,$profile_data, $confirm_account=true, $active=0, $role_id=1){
        list($error,$error_field) = $this->CheckRegisterFields($user_id,$login,$password,$password_confirm,$email);
        $message_class=false;
        $confirm_text='';
        $message_desc='';
        $message_inline=false;
        $text='';
	    $html='';
        if ($error=='') {
            list($error,$error_field) = $this->CheckProfileFields($profile_data);
            if ($error=='') {
                if ($user_id) {
                    $status=$this->UpdateUser($user_id,$login, $email, $active, $role_id);
                    $this->UpdateProfileData($user_id,$profile_data);
                    if ($password!='' OR $password_confirm!='') {
                        $this->UpdateUserPassword($user_id, $password);
                    }
                    $text = $this->lang['account_updated'];

                }
                else {
                    $user_id = $this->CreateUser($login, $password, $email, $active, $role_id);
                    if ($user_id) {
                        $status = true;
                        $message_inline=true;
                        $text = $this->lang['account_created'];
                        $this->AddProfileData($user_id, $profile_data);
                    }
                    else {
                        $status = false;
                        $text = $this->lang['user_create_error'];
                    }
                }

                if ($status==true && $confirm_account == true) {
                    if ($this->ProcessConfirmAccount($user_id)) {
                        $confirm_text = $this->registry->lang->SetVars(
                            $this->lang['confirm_send'],
                            array(
                                $email
                            )
                        );
                        $message_class = 'message';
                    } else {
                        $confirm_text = $this->lang['confirm_send_error'];
                        $message_class = 'error';
                        $message_desc = $this->lang['email_send_error_desc'];
                    }
                }
            }
            else {
                $status=false;
                $text=$error;
            }
        }
        else {
            $status=false;
            $text=$error;
        }

        if ($status) {
            if ($message_inline==true) {
                $html = $this->registry->template->Render('frontend/components/register-response/register-response.twig',
                    array(
                        'title' => $this->lang['account_created'],
                        'message' => $confirm_text,
                        'message_class' => $message_class,
                        'message_desc' => $message_desc
                    )
                );
            }
        }
        return array(
            'response'=>array(
                'status'=>$status,
                'text'=>$text,
                'html'=>$html,
                'error_field'=>$error_field
            ),
            'user_id'=>$user_id
        );
    }

    /**
     * @param $hash string
     * @return bool|mysqli_result|string
     */
    function DeleteUserHash ($hash){
        return $this->db->query_write("DELETE 
        FROM `users_hashes` 
        WHERE `hash`=".$this->db->sql_prepare($hash));
    }


    /**
     * @param $hash string
     * @param $type string
     * @return mixed
     */
    function SelectUserHash ($hash, $type) {
        return $this->db->query_first("SELECT * 
        FROM `users_hashes` 
        WHERE `type`=".$this->db->sql_prepare($type)." AND `hash`=".$this->db->sql_prepare($hash));
    }

    /**
     * @param $user_id int
     * @param $type string
     * @return mixed
     */
    function SelectUserHashByUserId ($user_id, $type) {
        return $this->db->query_first("SELECT * 
        FROM `users_hashes` 
        WHERE `type`=".$this->db->sql_prepare($type)." AND `user_id`=".$this->db->sql_prepare($user_id));
    }


    /**
     * @param $user_info array
     */
    function CheckUserActive($user_info){

        if ( $user_info['user_id'] && $user_info['user_active']==0 &&
            $this->registry->GPC['do']!='logout' && $this->registry->GPC['do']!='confirm' &&
            $this->registry->GPC['do']!='change_email' && $this->registry->GPC['do']!='js_lang'

        ) {
            if ($this->registry->GPC['ajax']) {
                $array=array();
                $array['redirect']=$this->registry->settings_values['links']['confirm'];
                $this->registry->template->DisplayJson($array);
            }
            else {
                Redirect_to($this->registry->settings_values['links']['confirm']);
            }
        }
    }

    /**
     * @param $user_id int
     * @return string
     */
    function CheckUserConfirm($user_id){
        $data=$this->SelectUserHashByUserId($user_id,'confirm');
        if ($data['hash']){
            if ($data['time']>=TIMENOW) {
                return 'ok';
            }
            else {
                return 'expired';
            }
        }
        else {
            return 'none';
        }
    }

    /**
     * @param $user_id int
     * @return bool|mysqli_result|string
     */
    function ActivateAccount ($user_id){
        return $this->db->query_write("UPDATE `users` 
        SET `user_active`=1 
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }
	/**
	 * @param $user_id int
	 * @return bool|mysqli_result|string
	 */
	function DeactivateAccount ($user_id){
		return $this->db->query_write("UPDATE `users` 
        SET `user_active`=0
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
	}
    /**
     * @return string
     */
    function NewEmailPageLink () {
        $url=BASE_URL.'/'.$this->options['new_email_route'].'/';
        return $url;
    }

    /**
     * @return string
     */
    function ConfirmPageLink () {
        $url=BASE_URL.'/'.$this->options['confirm_route'].'/';
        return $url;
    }

    /**
     * @return string
     */
    function ProfilePageLink () {
        $url=BASE_URL.'/'.$this->options['profile_route'].'/';
        return $url;
    }
    /**
     * @return string
     */
    function LogoutPageLink () {
        $url=BASE_URL.'/'.$this->options['logout_route'].'/';
        return $url;
    }

    /**
     * @return string
     */
    function LoginPageLink () {
        $url=BASE_URL.'/'.$this->options['login_route'].'/';
        return $url;
    }

    /**
     * @return string
     */
    function RegisterPageLink () {
        $url=BASE_URL.'/'.$this->options['register_route'].'/';
        return $url;
    }

    /**
     * @param $hash string
     * @return string
     */
    function ForgotProcessPageLink ($hash) {
        $url=BASE_URL.'/'.$this->options['forgot_process_route'].'/'.$hash.'/';
        return $url;
    }

    /**
     * @return string
     */
    function ForgotPageLink () {
        $url=BASE_URL.'/'.$this->options['forgot_route'].'/';
        return $url;
    }


    /**
     * @param $login string
     * @param $ip string
     * @param $time int
     * @return int
     */
    function CountStrike ($login, $ip, $time){
        $data=$this->db->query_first("SELECT count(id) as count
        FROM `users_strikes`
        WHERE `login`=".$this->db->sql_prepare($login)." AND 
        `ip`=".$this->db->sql_prepare($ip)." AND 
        `time`>".$this->db->sql_prepare($time));
        return intval($data['count']);
    }

    /**
     * @param $login string
     * @return bool
     */
    function CheckStrike ($login){
        $ip=$this->registry->input->alt_ip;
        $time=TIMENOW-$this->options['strike_time']*60;
        $count=$this->CountStrike($login,$ip,$time);
        if ($count>$this->options["max_strikes"]) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * @param $login string
     * @return bool
     */
    function CheckForgotStrike ($login){
        $this->options['strike_time']=$this->options['max_strikes']*2;
        return $this->CheckStrike($login);
    }

    /**
     * @param $login string
     * @param $ip string
     * @param $time int
     * @return bool|mysqli_result|string
     */
    function InsertStrike ($login, $ip, $time){
       return $this->db->query_write("INSERT INTO `users_strikes` (`login`,`ip`,`time`)
        VALUES (
        ".$this->db->sql_prepare($login).",
        ".$this->db->sql_prepare($ip).",
        ".$this->db->sql_prepare($time)."
        )");
    }

    /**
     * @param $login string
     * @return bool|mysqli_result|string
     */
    function LogStrike ($login){
        $ip=$this->registry->input->alt_ip;
        $time=TIMENOW;
        return $this->InsertStrike($login,$ip,$time);
    }

    function DeleteUserSessionByHash ($sessionhash){
        $this->db->query_write("DELETE FROM `users_session` 
        WHERE `sessionhash`=".$this->db->sql_prepare($sessionhash));
    }

    /**
     * @param $data array
     * @return mixed
     */
    function PrepareUserTemplate ($data){
        unset($data['user_password']);
        unset($data['user_salt']);
        return $data;
    }

    /**
     * @param $user_id int
     * @return bool|mysqli_result|string
     */
    function DeleteUserSession ($user_id){
        return $this->db->query_write("DELETE FROM `users_session` WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $user_id int
     */
    function LogOut ($user_id) {
        $this->DeleteUserSession($user_id);
        $this->SetUserCookie('','');
        //$this->UpdateUserSession($this->registry->user_info['sessionhash']);
    }


    /**
     * @param $login string
     * @return bool
     */
    function ProcessForgot ($login){
        $user_info=$this->GetUserByLogin($login);
        $this->DeleteForgotHash($user_info['user_id']);

        $hash=fetch_random_string();
        $status=$this->InsertForgotHash($user_info['user_id'],$hash);
        if ($status) {
            $forgot_link=$this->ForgotProcessPageLink($hash);
            $title=$this->lang['forgot_form'];
            $from_array = array($this->options['email_addr']=>$this->options['email_name']);
            $to_array = array($user_info['user_email'] => $user_info['user_login']);
            $body = $this->registry->template->Render('users/emails/forgot_account.html.twig',
                array(
                    'title' => $this->lang['account_forgot_email_title'],
                    'forgot_link' => $forgot_link
                )
            );
            if ($this->SendUserMail($title, $body, $from_array, $to_array)) {
                return true;
            }
            else {
                return false;
            }

        }
        else {
            return false;
        }
    }

    /**
     * @param $user_id int
     * @param $salt string
     * @param $password_db string
     * @return bool|mysqli_result|string
     */
    function UpdatePasswordDb ($user_id, $salt, $password_db){
        return $this->db->query_write("UPDATE `users` SET
        `user_salt`=".$this->db->sql_prepare($salt).",
        `user_password`=".$this->db->sql_prepare($password_db)."
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $user_id int
     * @param $password string
     * @return bool|mysqli_result|string
     */
    function UpdateUserPassword ($user_id, $password){
        $salt=GenerateName(10,4);
        $password_db=$this->CreateDbPassword($password,$salt);
        return $this->UpdatePasswordDb($user_id,$salt,$password_db);
    }


    /**
     * @param $user_id int
     * @param $new_email string
     * @return bool|mysqli_result|string
     */
    function UpdateUserEmail ($user_id, $new_email) {
        return $this->db->query_write("UPDATE `users` 
        SET `user_email`=".$this->db->sql_prepare($new_email).",
        `user_login`=".$this->db->sql_prepare($new_email)."
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @return array
     */
    function ProtectedRoles(){
        $protected_roles=array(
            '0'=>'guest',
            '1'=>'user',
            '2'=>'admin'
        );
        return $protected_roles;
    }

    /**
     * @param $role_id int
     * @return string
     */
    function GetUserRoleName ($role_id) {
        $roles_array=$this->ProtectedRoles();
        $user_role_name=$roles_array[$role_id];
        if ($user_role_name=='') {
            $user_role_name='guest';
        }
        return $user_role_name;
    }

    /**
     * @param string $roles_line
     * @return bool
     */
    function PagePrivacy ($roles_line=''){

        if ($roles_line=='') {
            $status=true;
        }
        else {
            $status = false;
            $good_roles = explode(',', $roles_line);
            $user_role = $this->GetUserRoleName($this->registry->user_info['user_role_id']);
            if (in_array($user_role, $good_roles)) {
                $status = true;
            } else {
                $this->registry->error->AccessDenied();
            }
        }
	   ////////////
        return $status;
    }


    /**
     * @param $filter_options array
     * @return string
     */
    function PrepareUsersWhere ($filter_options){
        $sql='';
        if (intval($filter_options['user_id'])>0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`users`.`user_id`=".$this->db->sql_prepare($filter_options['user_id']);
        }

        if ($filter_options['status']!=-1 ){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`users`.`user_active`=".$this->db->sql_prepare($filter_options['status']);
        }
        if ($filter_options['phone']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=' REPLACE(REPLACE(REPLACE(REPLACE(`users_profile`.`profile_phone`,"-",""),   "(",""),   ")",""),   " ","") LIKE "%'.$this->db->escape_string_like($filter_options['phone']).'%" ';
        }
        if ($filter_options['email']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.='`users`.`user_email` LIKE "%'.$this->db->escape_string_like($filter_options['email']).'%"';
        }

        if ($filter_options['city']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.='`users_profile`.`profile_city` LIKE "%'.$this->db->escape_string_like($filter_options['city']).'%"';
        }
        if ($filter_options['name']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=' profile_company  LIKE "%'.$this->db->escape_string_like($filter_options['name']).'%"';
        }

        if ($filter_options['date_start']!=''){
            $filter_options['date_start']=strtotime($filter_options['date_start'].' 00:00:00');
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `users`.`user_regtime`>=".$this->db->sql_prepare($filter_options['date_start']);
        }
	    if (intval($filter_options['user_role_id'])>0){
		    if ($sql != '') {
			    $sql .= ' AND ';
		    } else {
			    $sql .= ' WHERE ';
		    }
		    $sql.=" `users`.`user_role_id`=".$this->db->sql_prepare($filter_options['user_role_id']);
	    }


        if ($filter_options['date_end']!=''){
            $filter_options['date_end']=strtotime($filter_options['date_end'].' 23:59:59');
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `users`.`user_regtime`<=".$this->db->sql_prepare($filter_options['date_end']);
        }

        if ($filter_options['order_way']=='asc'){
            $filter_options['order_sort_sql']='ASC';
        }
        else {
            $filter_options['order_sort_sql']='DESC';
        }

        if ($filter_options['order']=='id'){
            $filter_options['order_sql']=' `users`.`user_id`';
        }
        elseif ($filter_options['order']=='email'){
            $filter_options['order_sql']=' `users`.`email`';
        }
        else {
            $filter_options['order_sql']=' `users`.`user_id`';
        }

        $sql.=" ORDER BY ".$filter_options['order_sql']." ".$filter_options['order_sort_sql'];
        return $sql;
    }

    /**
     * @param array $filter_options
     * @param $count int
     * @param $start_page int
     * @return bool|mysqli_result|string
     */
    function GetUsersFromDb ($count, $start_page){

        return $this->db->query_read("SELECT *, (SElECT COUNT(*) as orders_count from taxi_orders
           inner join taxi_orders_life
               on taxi_orders_life.life_order_id=taxi_orders.order_id
               and taxi_orders_life.life_order_status=4
           where order_phone=users.user_login ) as orders_count
        FROM `users` 
        LEFT JOIN `users_profile` ON `users`.`user_id`=`users_profile`.`profile_user_id` WHERE users.user_role_id=1 LIMIT ".$start_page.", ".$count);
    }
////    function GetUsersFromDb ($filter_options=array(), $count, $start_page){
//        $sql=$this->PrepareUsersWhere($filter_options);
//        return $this->db->query_read("SELECT *
//        FROM `users`
//        LEFT JOIN `users_profile` ON `users`.`user_id`=`users_profile`.`profile_user_id`
//        ".$sql."
//        LIMIT ".$start_page.",".$count);
//    }

    /**
     * @param array $filter_options
     * @param int $count
     * @param int $start_page
     * @return array
     */
    function GetUsers ($filter_options=array(), $count=10, $start_page=0) {
        $array=array();
        $result=$this->GetUsersFromDb($filter_options,$count,$start_page);
        while ($result_item = $this->db->fetch_array($result))
        {
            $result_item=$this->MakeProfileFields($result_item);
            $array[]=$result_item;
        }
        return $array;
    }


    /**
     * @param array $filter_options
     * @return int
     */
//    function GetUsersTotal ($filter_options=array()) {
//        $result=$this->GetUsersTotalFromDb($filter_options);
//        return intval($result['count']);
//    }
function GetUsersTotal ($filter_options=array()) {
        $result=$this->GetUsersTotalFromDb($filter_options);
        return intval($result['count']);
    }

    /**
     * @param $photo_path
     * @return string
     */
    function GetProfilePhotoUrl ($photo_path){
        if ($photo_path=='') {
            return BASE_URL.'/assets/images/core/no_photo.jpg';
        }
        else {
            return $this->registry->config['images']['path'].'/'.$photo_path;
        }
    }

    /**
     * @param $filter_options array
     * @return mixed
     */
    function GetUsersTotalFromDb(){

        $result = $this->db->query_first("SELECT count(`users`.`user_id`) as count
        FROM `users`
        LEFT JOIN `users_profile` ON `users`.`user_id`=`users_profile`.`profile_user_id`
         WHERE users.user_role_id=1");
        return intval($result['count']);
    }
//    function GetUsersTotalFromDb($filter_options){
//        $sql=$this->PrepareUsersWhere($filter_options);
//        return $this->db->query_first("SELECT count(`users`.`user_id`) as count
//        FROM `users`
//        LEFT JOIN `users_profile` ON `users`.`user_id`=`users_profile`.`profile_user_id`
//        ".$sql);
//    }

    /**
     * @param $user_id int
     * @return mixed
     */
    function GetUserProfileFromDb ($user_id){
        return $this->db->query_first("SELECT `users_profile`.*, taxi_classes.*, taxi_orders.*,
       (SELECT COUNT(*)  FROM taxi_orders
        WHERE order_status=4 ORDER BY order_time DESC)as order_count
FROM users_profile LEFT JOIN taxi_classes ON users_profile.profile_car_class=taxi_classes.class_id
LEFT JOIN taxi_orders ON taxi_orders.order_user_id=users_profile.profile_user_id AND taxi_orders.order_status=4
WHERE `users_profile`.`profile_user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $user_id int
     * @return mixed
     */
    function GetUserProfile ($user_id){
        if ($user_id>0) {
            $data=$this->GetUserProfileFromDb($user_id);
            $data=$this->MakeProfileFields($data);
            return $data;
        }
        else {
            return array();
        }
    }
    function GetUserProfileByPhone ($user_phone){
        if ($user_phone>0) {
            $data=$this->db->query_first("SELECT * FROM users_profile WHERE profile_phone=".$user_phone);
            $data=$this->MakeProfileFields($data);
            return $data;
        }
        else {
            return array();
        }
    }

    /**
     * @param $data array
     * @return array
     */
    function MakeProfileFields ($data) {
        $data['file_sizes']=unserialize($data['file_sizes']);
	    $data=$this->registry->files->FilePrepare($data,'photo_');
	    $data['item_icon_url'] = $this->registry->files->GetImageUrl($data,'medium',0,'icon_');
	    if ($data['profile_discounts']=='') {
		    $data['profile_discounts']=array();
	    }
	    else {
		    $data['profile_discounts']=unserialize($data['profile_discounts']);
	    }
        return $data;
    }


    /**
     * @param $user_id int
     * @param $data array
     * @return bool|mysqli_result|string
     */
    function AddProfileData ($user_id, $data) {
        if (count($data)>0) {
            $fields_sql = ','.implode(',',array_keys($data));
            $values=array();
            foreach ($data as $value) {
                $values[]=$this->db->sql_prepare($value);
            }
            $values_sql=','.implode(',',$values);
        }

        return $this->db->query_write("INSERT INTO `users_profile`
        (`profile_user_id`".$fields_sql.")
        VALUES (
        ".$this->db->sql_prepare($user_id).$values_sql.")"
        );
    }

    /**
     * @param $user_id int
     * @param $data array
     * @return bool|mysqli_result|string
     */
    function UpdateProfileData ($user_id, $data) {
        if (count($data)>0) {
            $values=array();
            foreach ($data as $key=>$value) {
                $values[]='`'.$key.'`='.$this->db->sql_prepare($value);
            }
            $values_sql=implode(',',$values);
            return $this->db->query_write("UPDATE `users_profile`
            SET ".$values_sql."
            WHERE `profile_user_id`=".$this->db->sql_prepare($user_id)."
            ");
        }
        else {
            return false;
        }

    }

    /**
     * @param $user_id int
     * @return bool|mysqli_result|string
     */
    function DeleteUserFromDb ($user_id) {
        return $this->db->query_write("DELETE FROM `users` 
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $user_id int
     * @return bool|mysqli_result|string
     */
    function DeleteProfileFromDb ($user_id) {
        return $this->db->query_write("DELETE FROM `users_profile` 
        WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
    }

    /**
     * @param $user_id int
     * @return bool|mysqli_result|string
     */
    function DeleteUserById ($user_id){
        $status=$this->DeleteUserFromDb($user_id);
        $this->DeleteProfileFromDb($user_id);
        return $status;
    }


    function CheckEmailField ($email){
        $error='';
        $error_field='';
        if ($email=='') {
            $error=$this->lang['enter_email'];
            $error_field='email';
        }
        elseif (is_valid_email($email)==false) {
            $error=$this->lang['enter_correct_email'];
            $error_field='email';
        }
        return array($error,$error_field);
    }

	function  UpdateOauth ($user_id,$oauth){
		$this->db->query_write("UPDATE `users`
        SET `user_oauth`=".$this->db->sql_prepare($oauth)."
        WHERE `user_id`=".$this->db->sql_prepare($user_id));
	}


	function UpdatePhone ($value){
		$this->db->query_write("UPDATE `users`
        SET `user_login`=".$this->db->sql_prepare($value)."
        WHERE `user_login`='".$Main->user_info['user_login']."'");
	}
	function UpdateBonus ($user_id,$value){
		$this->db->query_write("UPDATE `users_profile`
        SET `profile_bonus`=`profile_bonus`+".$this->db->sql_prepare($value)."
        WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
	}

	function UpdateUserSubscribe($user_id, $value) {
		$this->db->query_write("UPDATE `users_profile`
        SET `profile_subscribed`=".$this->db->sql_prepare($value)."
        WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
	}

	function UpdateUserNotify($user_id, $value=1) {
		$this->db->query_write("UPDATE `users_profile`
        SET `profile_notify`=".$this->db->sql_prepare($value)."
        WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
	}
	function UpUserNotify($user_id) {
		$this->db->query_write("UPDATE `users_profile`
        SET `profile_notify`=`profile_notify`+1
        WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
	}


	function getDiscount($price,$brand_id=0) {
    	if (isset($this->registry->user_profile['profile_discounts'][$brand_id])) {
		    $price =  $price - $price* ( $this->registry->user_profile['profile_discounts'][$brand_id]['value'] / 100 );
	    }
	    elseif ($this->registry->user_profile['profile_discount']) {
		    $price =  $price - $price* ( $this->registry->user_profile['profile_discount'] / 100 );
	    }

		return $price;
	}
}

