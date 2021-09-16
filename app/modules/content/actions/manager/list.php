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
if ($Main->GPC['action']=='update_status') {
	$Main->input->clean_array_gpc('r', array(
		'object_id'=>TYPE_UINT,
		'value'=>TYPE_BOOL
	));

	$Main->db->query_write('UPDATE core_content
		SET content_status='.$Main->db->sql_prepare($Main->GPC['value']).'
		WHERE content_id='.$Main->db->sql_prepare($Main->GPC['object_id']));

	$array=array();
	$array['status']=true;

		$array['text']='Статус обновлен';

	$Main->template->DisplayJson($array);
}

if ($Main->GPC['action']=='sort_table') {
	$Main->input->clean_array_gpc('r', array(
		'data_sort' => TYPE_ARRAY_UINT
	));

	$pos=0;
	foreach ($Main->GPC['data_sort'] as $line_key) {
		$Main->db->query_write('UPDATE core_content
		SET content_sort='.$Main->db->sql_prepare($pos).'
		WHERE content_id='.$Main->db->sql_prepare($line_key));
		$pos++;
	}

	$array['status']=true;
	$array['text']='Позиции обновлены';
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
elseif ($Main->GPC['content_type']=='articles'){
	$page_name='Статьи';
}
elseif ($Main->GPC['content_type']=='akcii'){
	$page_name='Акции';
}
elseif ($Main->GPC['content_type']=='services'){
	$page_name='Услуги';
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
    'content_cat' => TYPE_UINT,
    'cat_thumb'=>TYPE_INT,
    'cat_thumb3'=>TYPE_INT,
    'content_status' => TYPE_BOOL,
    'order' => TYPE_STR
));


if (!$Main->GPC['order']) {
	$Main->GPC['order']='sort';
}

$filter_options=array();
$filter_options['content_title']=$Main->GPC['content_title'];
$filter_options['content_id']=$Main->GPC['content_id'];
$filter_options['content_cat']=$Main->GPC['content_cat'];
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
if ($Main->GPC_exists['cat_thumb']){
	if ($Main->GPC['cat_thumb']==0){
		$filter_options['content_thumb']=false;
	}
	elseif ($Main->GPC['cat_thumb']==1){
		$filter_options['content_thumb']=true;
	}
}

if ($Main->GPC_exists['cat_thumb3']){
	if ($Main->GPC['cat_thumb3']==0){
		$filter_options['content_thumb3']=false;
	}
	elseif ($Main->GPC['cat_thumb3']==1){
		$filter_options['content_thumb3']=true;
	}
}
$pages=0;
if ($Main->GPC['content_type']=="pages")
{
    $pages=1;
}

$filter_options['show_order']=true;
$variables=$filter_options;
$variables['content_type']=$Main->GPC['content_type'];

$variables['cats']=$ContentClass->GetNewsCats();


$Paging =new ClassPaging($Main,50);
//if ($variables['content_type']=='akcii') {
////	$Paging->per_page=50;
//}

$Paging->data=$ContentClass->GetContentList($filter_options,$Paging->per_page,$Paging->sql_start,$pages);
$Paging->total=$ContentClass->GetContentListTotal($filter_options);
$Paging->show_per_page=true;
$Paging->Display('content/manager/content_table.html.twig',$variables);
