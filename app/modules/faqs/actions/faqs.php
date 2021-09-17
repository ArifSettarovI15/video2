<?php
require_once ROOT_DIR . '/app/modules/users/init.php';
$Main->user->PagePrivacy('admin');

if ($Main->GPC['action']=='delete') {
	$Main->input->clean_array_gpc('r', array(
		'object_id' => TYPE_UINT
	));

	$banner_info=$Faqs->GetItemById($Main->GPC['object_id']);

    $Faqs->CreateModel();
    $Faqs->model->columns_where->getId()->setValue($Main->GPC['object_id']);
	$status=$Faqs->Delete();

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

if ($Main->GPC['action']=='update_status') {
	$Main->input->clean_array_gpc('r', array(
		'object_id'=>TYPE_UINT,
		'value'=>TYPE_BOOL
	));

	$banner_info=$Faqs->GetItemById($Main->GPC['object_id']);

    $Faqs->CreateModel();
    $Faqs->model->columns_where->getId()->setValue($Main->GPC['object_id']);
    $Faqs->model->columns_update->getStatus()->setValue($Main->GPC['value']);

	$status=$Faqs->Update();

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

$page_name='Faq';
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
				'title'=>$page_name
			)
		),
		'title'=>$page_name
	)
);

$Faqs->CreateModel();
$Faqs->model->setOrderByWithColumn($Faqs->model->columns_where->getTitle()->getName());
$list=$Faqs->GetList();

$Main->template->Display(array(
		'list'=>$list
	)
);

