<?php
$Main->user->PagePrivacy('admin');
if ($Main->GPC['action']=='delete') {
	$Main->input->clean_array_gpc('r', array(
		'object_id' => TYPE_UINT
	));
	$status=$ContentClass->DeleteContent($Main->GPC['object_id']);
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

$breadcrumbs=array();
$child_content=1;
$breadcrumbs[]=array(
	'title'=>'Контент',
	'link'=>BASE_URL.'/manager/content/'
);
if ($Main->GPC['content_type']=='pages'){
	$page_name='Страницы';
}
elseif ($Main->GPC['content_type']=='news'){
	$page_name='Новости';
}
else {
	$page_name='Контент';
	$child_content=0;
}

if ($child_content==1) {
	$breadcrumbs[]=array(
		'title'=>$page_name,
		'link'=>BASE_URL.'/manager/content/'.$Main->GPC['content_type'].'/'
	);
}

$Main->template->SetPageAttributes(
	array(
		'title'=>$page_name,
		'keywords'=>'',
		'desc'=>''
	),
	array(
		'breadcrumbs'=>$breadcrumbs,
		'title'=>$page_name
	)
);

$Main->input->clean_array_gpc('r', array(
	'content_title' => TYPE_STR,
	'content_id' => TYPE_UINT,
	'content_status' => TYPE_BOOL,
	'order' => TYPE_STR
));
$filter_options=array();
$filter_options['content_title']=$Main->GPC['content_title'];
$filter_options['content_id']=$Main->GPC['content_id'];
$filter_options['content_status']=$Main->GPC['content_status'];
$filter_options['order']=$Main->GPC['order'];
$filter_options['content_type']=$Main->GPC['content_type'];
$filter_options['skip_date']=true;
if ($Main->GPC_exists['content_status']){
	$filter_options['content_status']=$Main->GPC['content_status'];
}
else {
	$filter_options['content_status']=-1;
}
$filter_options['show_order']=true;
$variables=$filter_options;
$variables['content_type']=$Main->GPC['content_type'];


$Paging =new ClassPaging($Main,15);
$Paging->data=$ContentClass->GetContentList($filter_options,$Paging->per_page,$Paging->sql_start);
$Paging->total=$ContentClass->GetContentListTotal($filter_options);
$Paging->show_per_page=true;
$Paging->Display('content/manager/content_table.html.twig',$variables);