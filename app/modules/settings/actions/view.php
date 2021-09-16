<?php
$Main->user->PagePrivacy('admin');


if ($Main->GPC['action']=='delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_UINT
    ));
    $status=$SettingsClass->DeleteGroupSetting($Main->GPC['object_id']);
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
if ($Main->GPC['action']=='update_badge_status') {
	$Main->input->clean_array_gpc('r', array(
		'object_id'=>TYPE_UINT,
		'value'=>TYPE_BOOL,
		'type_id'=>TYPE_STR
	));

	$status=$SettingsClass->UpdateGroupBadge($Main->GPC['type_id'],$Main->GPC['object_id'],$Main->GPC['value']);


	$array=array();
	$array['status']=$status;
	if ($status) {
		$array['text']='Значение обновлено';
	}
	else {
		$array['text']='Ошибка';
	}
	$Main->template->DisplayJson($array);
}

$page_name='Группы полей';
$breadcrumbs=array();
$breadcrumbs[]=array(
    'title'=>'Группы полей'
);
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
    'title' => TYPE_STR,
    'id' => TYPE_UINT,
    'status' => TYPE_NUM
));
$filter_options=array();
$filter_options['title']=$Main->GPC['title'];
$filter_options['id']=$Main->GPC['id'];
if ($Main->GPC_exists['status']){
    $filter_options['status']=$Main->GPC['status'];
}
else {
    $filter_options['status']=-1;
}

$variables=array();
$variables['settings']=$settings;

$Paging =new ClassPaging($Main,20,false,false);
$Paging->total=$SettingsClass->GetGroupSettingsTotal($filter_options);
$filter_options['show_order']=true;
$Paging->data=$SettingsClass->GetGroupSettings($filter_options,$Paging->per_page,$Paging->sql_start);
$Paging->show_per_page=true;
$Paging->Display('settings/manager/settings_table.html.twig',$variables);
