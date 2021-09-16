<?php
$Main->user->PagePrivacy('admin');

$group_info=$SettingsClass->GetGroupSetting($Main->GPC['group_id']);
if ($group_info) {

}
else {
    $Main->error->ObjectNotFound();
}

$page_name=$group_info['cs_group_name'];
$breadcrumbs=array();
$breadcrumbs[]=array(
    'title'=>'Группы полей',
    'link'=>BASE_URL.'/manager/settings/'
);
$breadcrumbs[]=array(
    'title'=>$page_name
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


if ($Main->GPC['action']=='delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_UINT
    ));
    $status=$SettingsClass->DeleteSetting($Main->GPC['object_id']);
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
if ($Main->GPC['action']=='UpdateSort') {
    $Main->input->clean_array_gpc('r', array(
        'tree' => TYPE_ARRAY,
        'changed_id'=>TYPE_UINT
    ));
    $array = array();
    if ( $SettingsClass->ProcessSettingsSort($Main->GPC['tree'],1,0)) {
        $array['status']=true;
    }
    else {
        $array['status']=false;
    }
    $Main->template->DisplayJson($array);
}

$options=array();
$options['show_order']=true;
$options['order']='sort';
$options['group_id']=$Main->GPC['group_id'];
$fields=$SettingsClass->GetSettingsFields($options,'all');
$fields=$SettingsClass->MakeFieldsTree(0,$fields);
$fields['group_info']=$group_info;
$fields = $SettingsClass->MakeFieldsTreeUl($fields);
$Main->template->Display(array(
        'fields'=>$fields,
        'group_info'=>$group_info
    )
);
