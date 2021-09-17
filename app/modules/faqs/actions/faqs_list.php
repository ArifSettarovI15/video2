<?php
require_once ROOT_DIR . '/app/modules/users/init.php';
$Main->user->PagePrivacy('admin');

$faqs_info=$Faqs->GetItemById($Main->GPC['id'],1);
if ($faqs_info) {

}
else {
	$Main->error->ObjectNotFound();
}

if ($Main->GPC['action']=='delete') {
	$Main->input->clean_array_gpc('r', array(
		'object_id' => TYPE_UINT
	));

	$banner_info=$FaqsList->GetItemById($Main->GPC['object_id']);

	$FaqsList->CreateModel();
	$FaqsList->model->columns_where->getId()->setValue($Main->GPC['object_id']);
	$status=$FaqsList->Delete();

	$array=array();
	$array['status']=$status;
	if ($status) {
		$array['text']='Объект успешно удален';
	}
	else {
		$array['text']='Ошибка удаления объекта';
	}
	$Main->template->DisplayJson($array);
}

if ($Main->GPC['action']=='sort_table') {
	$Main->input->clean_array_gpc('r', array(
		'data_sort' => TYPE_ARRAY_UINT
	));

	$pos=0;
	$FaqsList->CreateModel();

	foreach ($Main->GPC['data_sort'] as $line_key) {
		$FaqsList->model->columns_where->getId()->setValue($line_key);
		$FaqsList->model->columns_update->getSort()->setValue($pos);
		$FaqsList->Update();
		$pos++;
	}

	$array['status']=true;
	$array['text']='Позиции обновлены';
	$Main->template->DisplayJson($array);
}

if ($Main->GPC['action']=='update_status') {
	$Main->input->clean_array_gpc('r', array(
		'object_id'=>TYPE_UINT,
		'value'=>TYPE_BOOL
	));

	$banner_info=$FaqsList->GetItemById($Main->GPC['object_id']);

	$FaqsList->CreateModel();
	$FaqsList->model->columns_where->getId()->setValue($Main->GPC['object_id']);
	$FaqsList->model->columns_update->getStatus()->setValue($Main->GPC['value']);

	$status=$FaqsList->Update();

	$array=array();
	$array['status']=$status;
	if ($status) {
		$array['text']='Статус обновлен';
	}
	else {
		$array['text']='Ошибка';
	}
	$Main->template->DisplayJson($array);
}




/////////////////////////////////
$Main->input->clean_array_gpc('r', array(

));

$variables=array();

$page_name=$faqs_info['faqs_title'];
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
				'title'=>'FAQ',
				'link'=>BASE_URL.'/manager/tiger/faqs/'
			),
			array(
				'title'=>$page_name
			)
		),
		'title'=>$page_name
	)
);


$FaqsList->CreateModel();
$FaqsList->model->columns_where->getFaq()->setValue($Main->GPC['id']);
$FaqsList->model->setOrderByWithColumn($FaqsList->model->columns_where->getSort()->getName());
$list=$FaqsList->GetList();


$Main->template->Display(array(
		'list'=>$list,
		'faqs_info'=>$faqs_info
	)
);
