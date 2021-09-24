<?php


$Main->user->PagePrivacy('guest');
$error = '';
$error_desc = '';
$mes = '';
$error_field = '';
$user_info = array();
$inline = false;
$expire = false;
if($Main->GPC['action'] == 'process_confirm'){
    $Main->input->clean_array_gpc('r', array(
        'phone' => TYPE_STR,
        'number' => TYPE_UINT,
    ));

    $Main->GPC['phone'] = $res = preg_replace("/[^0-9]/", "", $Main->GPC["phone"] );

    if ($Main->GPC['phone'] == '' and $Main->GPC['number'] == '') {
        $error = "Ошибка проверки данных попробуйте еще раз.";
    }
    elseif ($Main->user->CheckStrike($Main->GPC["phone"])) {
        $error = $Main->lang->data['users']['strike_error'];
    }
    else {
        if ($Main->GPC['phone'] != '') {
            $user_info = $Main->user->GetUserByLogin($Main->GPC["phone"], false);
            $user_info['user_password'] = $Main->db->query_first('SELECT code_user_code FROM users_codes WHERE code_confirmed=0 AND code_user_phone='.$Main->db->sql_prepare($Main->GPC['phone']));
            $user_info['user_password'] = $user_info['user_password']['code_user_code'];
        }
        if ($user_info['user_active'] and $error=='') {
            if (intval($user_info['user_id']) == 0) {
                $Main->user->LogStrike($Main->GPC['phone']);
                $error = $Main->lang->data['users']['login_error2'];
                $error_field = 'email,password';
            }
            elseif ($user_info['user_password'] != $Main->GPC['number']){

                $error = "Вы ввели не верный код, попробуйте ещё!";
            }
            else{
                $user_info['user_password'] = $Main->db->query_first('SELECT code_user_code FROM users_codes WHERE code_confirmed=0 AND code_user_phone='.$Main->db->sql_prepare($Main->GPC['phone']));
                $user_info['user_password'] = $user_info['user_password']['code_user_code'];
                if ($user_info['user_password'] == $Main->GPC['number']){
                    $Main->db->query_write('UPDATE users_codes SET code_confirmed=1 WHERE code_user_phone='.$Main->db->sql_prepare($Main->GPC['phone']));
                    $user_info['sessionhash'] = $Main->user_info['sessionhash'];
                    $Main->user->UpdateUserSession($Main->user_info['sessionhash'], $user_info['user_id'], $expire);
                    $Main->user_info = $user_info;
                    $Main->template->global_vars['user_info'] = $Main->user->PrepareUserTemplate($Main->user_info);
                    $Main->user_profile = $Main->user->GetUserProfile($Main->user_info['user_id']);
                    $Main->template->global_vars['user_profile'] = $Main->user_profile;
                }

                elseif ($user_info['user_password'] != $Main->GPC['number']){

                    $error = "Вы ввели не верный код, попробуйте ещё!";
                }
            }
        }
        else{

            $error='';
            $login = $Main->GPC['phone'];
            $active = 1;
            $role_id = 1;
            $password = $Main->user->generateRandomString(8);
            $email = "";
            $user = $Main->user->CreateUser($login, $password, $email, $active, $role_id);


            $data = array(
                'profile_phone' => $Main->GPC['phone']
            );
            $user_profile = $Main->user->AddProfileData($user, $data);
            $user_info = $Main->user->GetUserByLogin($Main->GPC["phone"], false);
            $user_info['sessionhash'] = $Main->user_info['sessionhash'];
            $Main->user->UpdateUserSession($Main->user_info['sessionhash'], $user_info['user_id'], $expire);
            $Main->user_info = $user_info;
            $Main->template->global_vars['user_info'] = $Main->user->PrepareUserTemplate($Main->user_info);
            $Main->user_profile = $Main->user->GetUserProfile($Main->user_info['user_id']);
            $Main->template->global_vars['user_profile'] = $Main->user_profile;
        }
    }
    $reload = false;
    $text = '';
    $html = '';
    $redirect = '';
    if ($error == '') {
        $redirect = BASE_URL . '/cabinet/';
        $status = true;
        //    $html= $Main->template->Render('parts/auth.html.twig');
        $reload = true;
    } else {
        $status = false;
        if ($inline == true) {
            $text = $Main->template->Render('global/message.html.twig',
                array(
                    'message' => $error,
                    'message_class' => 'error',
                    'message_desc' => $error_desc
                )
            );
        } else {
            $text = $error;
        }
    }

    $array_json = array(
        'status' => $status,
        'message_inline' => $inline,
        'redirect' => $redirect,
        'text' => $text,
        'html' => $html,
        'error_field' => $error_field
    );

    $Main->template->DisplayJson($array_json);

}

if ($Main->GPC['action'] == 'process_login') {

    $Main->input->clean_array_gpc('r', array(
        'login' => TYPE_STR,
        'password' => TYPE_NOTRIM
    ));

    $error = '';
    $error_desc = '';
    $mes = '';
    $error_field = '';
    $user_info = array();
    $inline = false;
    $expire = false;
    if ($Main->GPC['phone'] == '' and $Main->GPC['login'] == '') {
        $error = $Main->lang->data['users']['enter_login'];
        $error_field = 'login';
    }
    elseif ($Main->user->CheckStrike($Main->GPC["phone"])) {
        $error = $Main->lang->data['users']['strike_error'];
    }
    else {

        if ($Main->GPC['login'] != '') {
            $user_info = $Main->user->GetUserByLogin($Main->GPC["login"], false);
            $redirect_url = '/cabinet/';
        }

        if ($user_info['user_active'] and $error == '') {
            if (intval($user_info['user_id']) == 0) {
                $Main->user->LogStrike($Main->GPC['phone']);
                $error = $Main->lang->data['users']['login_error2'];
                $error_field = 'email,password';
            } elseif ($Main->GPC['login'] != '' and $Main->GPC['phone'] == '') {

                if (sha1($user_info['user_salt'] . sha1($user_info['user_salt'] . sha1($Main->GPC['password']))) != $user_info['user_password']) {
                    $Main->user->LogStrike($Main->GPC['phone']);
                    $error = $Main->lang->data['users']['login_error2'];
                    $error_field = 'email,password';

                } else {

                    // $Main->user->DeleteUserSessionByHash($Main->user_info['sessionhash']);
                    $Main->user->CreateUserSession($Main->user_info['user_id']);
                    $user_info['sessionhash'] = $Main->user_info['sessionhash'];
                    $Main->user->UpdateUserSession($Main->user_info['sessionhash'], $user_info['user_id'], $expire);
                    $Main->user_info = $user_info;
                    $Main->template->global_vars['user_info'] = $Main->user->PrepareUserTemplate($Main->user_info);
                    $Main->user_profile = $Main->user->GetUserProfile($Main->user_info['user_id']);
                    $Main->template->global_vars['user_profile'] = $Main->user_profile;


                }
            }
            elseif ($Main->GPC['phone'] != '' and $Main->GPC['login'] == '') {
                $array['phone'] = $Main->GPC['phone'];
                $array['result'] = $Main->template->Render('frontend/components/modal-login/modal-login_step1.twig', array('phone' => $array['phone']));
                $array['status'] = true;
                $code = rand(1000, 9999);
	            $Main->db->query_write("INSERT INTO users_codes (code_user_phone, code_user_code) VALUES (
				" . $Main->db->sql_prepare($Main->GPC['phone']) . ",
	             " . $Main->db->sql_prepare($code). ")");

	            $check_spam = $Main->db->query_first('SELECT COUNT(*) as count FROM taxi_spam
            WHERE spam_ip=' . $Main->db->sql_prepare($Main->input->fetch_alt_ip()) . ' AND
            spam_time>=' . $Main->db->sql_prepare(time() - 7200));
	            if ($check_spam['count'] >= 10) {
		            $Main->template->DisplayJson(array('status'=>false, 'text'=>'Ошибка. Попробуйте зайти позже'));
	            }
	            $Main->db->query_write('INSERT INTO taxi_spam (spam_time, spam_ip)
VALUES(
 ' . $Main->db->sql_prepare(TIMENOW) . ',
  ' . $Main->db->sql_prepare($Main->input->fetch_alt_ip()) . '
)');
//	            $sended = $Taxi->smsru->sendSms($Main->GPC['phone'], $code);


                $Main->template->DisplayJson($array);

            }
        } else {
            if ($user_info) {
                $error = 'Ваш аккаунт еще не активирован';
            } else {
                $error = 'Нет пользователя с таким логином!';
            }
        }
    }
        $reload = false;
        $text = '';
        $html = '';
        $redirect = '';
        if ($error == '') {
            $redirect = BASE_URL . $redirect_url;
            $status = true;
            //    $html= $Main->template->Render('parts/auth.html.twig');
            $reload = true;
        } else {
            $status = false;
            if ($inline == true) {
                $text = $Main->template->Render('global/message.html.twig',
                    array(
                        'message' => $error,
                        'message_class' => 'error',
                        'message_desc' => $error_desc
                    )
                );
            } else {
                $text = $error;
            }
        }

        $array_json = array(
            'status' => $status,
            'message_inline' => $inline,
            'redirect' => $redirect,
            'text' => $text,
            'html' => $html,
            'error_field' => $error_field
        );

        $Main->template->DisplayJson($array_json);


        }


if ($Main->GPC['action'] == 'process_register') {
    $Main->input->clean_array_gpc('r', array(
        'login' => TYPE_STR,
        'email' => TYPE_STR,
        'password' => TYPE_STR,
        'password_confirm' => TYPE_STR
    ));

    if ($Main->GPC['login'] == '') {
        $Main->template->DisplayJson(array('status' => false,
            'error' => 'Введите логин'));
    } else {
        $check = $Main->user->GetUserByLogin($Main->GPC['login']);
        $check2 = $Main->user->GetUserByEmail($Main->GPC['email']);
        if ($check)
        {
            $array['status'] = false;
            $array['text'] = 'Указанный логин уже зарегистрирован в системе!';
            $Main->template->DisplayJson($array);
        }
        elseif ($check2)
        {
            $array['status'] = false;
            $array['text'] = 'Указанный email уже зарегистрирован в системе!';
            $Main->template->DisplayJson($array);
        }
        elseif ($Main->GPC['password'] != $Main->GPC['password_confirm'])
        {
            $array['status'] = false;
            $array['text'] = 'Указанный email уже зарегистрирован в системе!';
            $Main->template->DisplayJson($array);
        }
        else{
        $login = $Main->GPC['login'];
        $active = 0;
        $role_id = 1;
        $password = $Main->GPC['password'];
        $email = $Main->GPC['email'];
        $user = $Main->user->CreateUser($login, $password, $email, $active, $role_id);
        if ($Main->GPC['name']){
            $fio_data = $Main->user->userNameExplode($Main->GPC['name']);
        }
        if (is_array($fio_data)){
            $name = $fio_data[0];
            $lastname = $fio_data[1];
        }else{$name = $fio_data;$lastname='';}
        $data = array(
            'profile_phone' => $Main->GPC['phone'],
            'profile_name' => $name,
            'profile_lastname' => $lastname,
        );
        $user_profile = $Main->user->AddProfileData($user, $data);
            $user = $Main->user->GetUserById($user);
            $title   = 'Регистраиця на сайте SNGTraining';
            $mail_to = $user['user_email'];
            $body = $Main->template->Render( 'static/email_register.twig',
                array(
                    'link'     => BASE_URL.'/confirmation/'.'?user='.$user['user_login'].'&confirm='.md5($user['user_email'])

                )
            );
            $aa = array( $Main->config['system']['email_addr'] => $Main->template->global_vars['fields']['about']['about_title'] );

            $message = ( new Swift_Message( $title ) )
                ->setFrom( $aa )
                ->setTo( [ $mail_to ] )
                ->setBody( $body, 'text/html' );


            try {
                $result = $Main->mailer->send( $message );
            } catch ( \Swift_TransportException $e ) {
                $response = $e->getMessage();
            }

        $array['text'] = 'Вы успешно зарегистрировались';
        $array['status'] = true;
        $Main->template->DisplayJson($array);

        }
    }
}


