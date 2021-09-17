<?php
require_once ROOT_DIR . '/app/modules/users/init.php';
$Main->user->PagePrivacy('admin');


$faqs_info=$Faqs->GetItemById($Main->GPC['id'],1);
if ($faqs_info) {

}
else {
	$Main->error->ObjectNotFound();
}

$data_info=array();
$edit=0;
if ($Main->GPC['action']=='process_edit' && $Main->GPC['do']!='edit') {
	$Main->input->clean_array_gpc('r', array(
		'fid' => TYPE_UINT
	));
}

if ($Main->GPC['do']=='edit' OR $Main->GPC['action']=='process_edit') {
	$edit=1;
	$data_info=$FaqsList->GetItemById($Main->GPC['fid'],1);
	if ($data_info) {

	}
	else {
		$Main->error->ObjectNotFound();
	}
}

if ($Main->GPC['action']=='process_add'  OR $Main->GPC['action']=='process_edit') {
	$Main->input->clean_array_gpc('r', array(
		'fitem_id' => TYPE_UINT,
		'fitem_ask'=>TYPE_STR,
		'fitem_answer'=>TYPE_STR,
	));

	$error='';
	$array=array();

	if ($Main->GPC['fitem_ask']=='') {
		$error='Введите вопрос';
		$array['error_field']='fitem_ask';
	}
	elseif ($Main->GPC['fitem_answer']=='') {
		$error='Введите ответ';
		$array['error_field']='fitem_answer';
	}
	else{


		$FaqsList->CreateModel();
		$FaqsList->model->columns_update->getAsk()->setValue($Main->GPC['fitem_ask']);
		$FaqsList->model->columns_update->getAnswer()->setValue($Main->GPC['fitem_answer']);
		$FaqsList->model->columns_update->getFaq()->setValue($faqs_info['faqs_id']);

		if ($Main->GPC['action'] == 'process_edit') {
			$id=$Main->GPC['fitem_id'];

			$FaqsList->model->columns_where->getId()->setValue($Main->GPC['fitem_id']);
			$result=$FaqsList->Update();

			if ($result ) {
				$array['status'] = true;
				$array['text'] = 'Значение успешно обновлено';
			} else {
				$array['text'] = 'Ошибка обновления';
			}

		} else {
			$id=$FaqsList->Insert();
			$array['text'] = 'Значение успешно добавлено';
			$array['status'] = true;
		}
	}

	if ($error!='') {
		$array['status']=false;
		$array['text']=$error;
	}
	else {
		$array['status']=true;
		//$array['redirect']=BASE_URL.'/manager/tiger/sections/';
	}
	$Main->template->DisplayJson($array);
}

if ($edit==1) {
	$a_name='Редактировать';
}
else {
	$a_name='Добавить';
}

$page_name=$a_name.' faq позицию';
$Main->template->SetPageAttributes(
	array(
		'title'=>$page_name,
		'keywords'=>'',
		'desc'=>''
	),
	array(
		'breadcrumbs'=>array(
			array(
				'title'=>'Студия'
			),
			array(
				'title'=>'FAQS',
				'link'=>BASE_URL.'/manager/tiger/faqs/'
			),
			array(
				'title'=>$faqs_info['faqs_title'],
				'link'=>BASE_URL.'/manager/tiger/faqs/view/'.$faqs_info['block_id'].'/'
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
		'faqs_info'=>$faqs_info,
		'edit'=>$edit
	)
);
