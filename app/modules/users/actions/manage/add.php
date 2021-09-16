<?php
$Main->user->PagePrivacy('admin');

$data_info=array();
$edit=0;


if ($Main->GPC['action']=='process_edit' && $Main->GPC['do']!='edit') {
	$Main->input->clean_array_gpc('r', array(
		'id' => TYPE_UINT
	));
}

if ($Main->GPC['do']=='edit' OR $Main->GPC['action']=='process_edit') {
	$edit=1;
	$data_info=$Main->user->GetUserById($Main->GPC['id']);
	if ($data_info) {

	}
	else {
		$Main->error->ObjectNotFound();
	}
}

if ($Main->GPC['action']=='process_add'  OR $Main->GPC['action']=='process_edit') {
	$Main->input->clean_array_gpc('r', array(
		'user_id' => TYPE_UINT,
		'user_email' => TYPE_STR,
		'user_password' => TYPE_STR,
		'user_password2' => TYPE_STR,
	));

	$error='';
	$array=array();




		if ($Main->GPC['action'] == 'process_edit') {

			$array['status'] = true;
			$array['text'] = 'Значение успешно обновлено';

			if ($Main->GPC['user_password']!='' and $Main->GPC['user_password2']!='' and $Main->GPC['user_password']==$Main->GPC['user_password2']) {
				$Main->user->UpdateUserPassword($data_info['user_id'],$Main->GPC['user_password']);
				$array['text'].=' Пароль успешно обновлен';

				$from_array = array(
					$Main->config['system']['email_addr'] => $Main->template->global_vars['fields']['about']['about_name']
				);
				$to_array = array($data_info['user_email']);
				$body = $Main->template->Render('users/emails/new_pass.twig',
					array(
						'password' => $Main->GPC['user_password'],
						'login' => $data_info['user_email']
					)
				);
				if ($Main->user->SendUserMail('Пароль обновлен', $body, $from_array, $to_array)) {
					$array['status'] = true;
					$array['text'] = 'Пароль обновлен';
					$array['redirect']='/manager/users/view/'.$data_info['user_id'].'/';
				}
			}

		} else {




			$register_data = $Main->user->SaveUser(
				$data_info['user_id'],
				$Main->GPC['user_email'],
				$Main->GPC['user_password'],
				$Main->GPC['user_password2'],
				$Main->GPC['user_email'],
				array(),
				0,
				1,
				1
			);

			if ($register_data['user_id'] ) {
				$from_array = array(
					$Main->config['system']['email_addr'] => $Main->template->global_vars['fields']['about']['about_name']
				);
				$to_array = array($Main->GPC['user_email']);
				$body = $Main->template->Render('users/emails/new_account.twig',
					array(
						'password' => $Main->GPC['user_password'],
						'login' => $Main->GPC['user_email']
					)
				);
				if ($Main->user->SendUserMail('Аккаунт создан', $body, $from_array, $to_array)) {
					$array['status'] = true;
					$array['text'] = 'Значение успешно добавлено';
					$array['redirect']='/manager/users/view/'.$register_data['user_id'].'/';
				}

			} else {
				$array['text'] = $register_data['response']['text'];
			}
		}




	if ($error!='') {
		$array['status']=false;
		$array['text']=$error;
	}
	else {
		$array['status']=true;
	}
	$Main->template->DisplayJson($array);
}

if ($edit==1) {
	$a_name='Редактировать';
}
else {
	$a_name='Добавить';
}

$page_name=$a_name.' пользователя';
$Main->template->SetPageAttributes(
	array(
		'title'=>$page_name,
		'keywords'=>'',
		'desc'=>''
	),
	array(
		'breadcrumbs'=>array(
			array(
				'title'=>'Полизователи',
				'link'=>BASE_URL.'/manager/users/'
			),
			array(
				'title'=>$a_name
			),
		),
		'title'=>$page_name
	)
);

$Main->template->Display(array(
		'info'=>$data_info,
		'profile'=>$Main->user->GetUserProfile($data_info['user_id']),
		'edit'=>$edit
	)
);
