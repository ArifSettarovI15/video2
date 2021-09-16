<?php
$Main->user->PagePrivacy('admin');

$group_info=$SettingsClass->GetGroupSetting($Main->GPC['group_id']);
if ($group_info) {

}
else {
    $Main->error->ObjectNotFound();
}


$data_info=array();
$edit=0;
if ($Main->GPC['action']=='process_edit' && $Main->GPC['do']!='edit') {
    $Main->input->clean_array_gpc('r', array(
        'id' => TYPE_UINT
    ));
}

if ($Main->GPC['do']=='edit' OR $Main->GPC['action']=='process_edit') {
    $edit=1;
    $data_info=$SettingsClass->GetSetting($Main->GPC['id']);
    if ($data_info) {

    }
    else {
        $Main->error->ObjectNotFound();
    }
}

if ($Main->GPC['action']=='process_add'  OR $Main->GPC['action']=='process_edit') {
    $Main->input->clean_array_gpc('r', array(
        'parent_id'=>TYPE_UINT,
        'title' => TYPE_STR,
        'key' => TYPE_STR,
        'status' => TYPE_BOOL,
        'id'=>TYPE_UINT,
        'caption'=>TYPE_STR,
        'type'=>TYPE_STR,
        'required'=>TYPE_BOOL,
        'visible'=>TYPE_BOOL
    ));
    $error='';
    $array=array();

    if ($Main->GPC['title']=='') {
        $error='Введите название';
        $array['error_field']='title';
    }
    elseif ($Main->GPC['key']=='') {
        $error='Введите ключ';
        $array['error_field']='key';
    }
    elseif ($Main->GPC['type']=='') {
        $error='Выберите поле';
        $array['error_field']='type';
    }
    else{
        $info = $SettingsClass->GetSettingsByTitle($group_info['cs_group_id'],$Main->GPC['title'],$Main->GPC['parent_id']);
        if ($info && $info['cs_id'] != $Main->GPC['id']) {
            $error = 'Такое поле уже существует';
            $array['error_field'] = 'title';
        } else {
            $info = $SettingsClass->GetSettingsByKey2($group_info['cs_group_id'],$Main->GPC['key']);
            if ($info && $info['cs_id'] != $Main->GPC['id']) {
                $error = 'Такой ключ уже существует';
                $array['error_field'] = 'key';
            }
            else {
                $parent_info=$SettingsClass->GetSetting($Main->GPC['parent_id']);
                $level=$parent_info['cs_level']+1;
                if ($Main->GPC['action'] == 'process_edit') {
                    $id = $Main->GPC['id'];
                    if ($SettingsClass->UpdateSettings($Main->GPC['id'], $group_info['cs_group_id'],$Main->GPC['title'], $Main->GPC['key'],$Main->GPC['status'],$Main->GPC['caption'],$Main->GPC['type'],$Main->GPC['required'],$Main->GPC['visible'],$level,$Main->GPC['parent_id'])) {
                        $array['status'] = true;
                        $array['text'] = 'Значение успешно обновлено';
                    } else {
                        $array['text'] = 'Ошибка обновления';
                    }

                } else {

                    $id = $SettingsClass->AddSettings($group_info['cs_group_id'],$Main->GPC['title'], $Main->GPC['key'],$Main->GPC['status'],$Main->GPC['caption'],$Main->GPC['type'],$Main->GPC['required'],$Main->GPC['visible'],$level,$Main->GPC['parent_id']);

                    $array['text'] = 'Значение успешно добавлено';
                    $array['status'] = true;
                }
            }
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

$page_name=$a_name.' поле';
$Main->template->SetPageAttributes(
    array(
        'title'=>$page_name,
        'keywords'=>'',
        'desc'=>''
    ),
    array(
        'breadcrumbs'=>array(
            array(
                'title'=>'Группы полей',
                'link'=>BASE_URL.'/manager/settings/'
            ),
            array(
                'title'=>$group_info['cs_group_name'],
                'link'=>BASE_URL.'/manager/settings/view/'.$group_info['cs_group_id'].'/'
            ),
            array(
                'title'=>$page_name
            ),
        ),
        'title'=>$page_name
    )
);

$types=$SettingsClass->GetSettingsFieldTypes();

$options=array();
$options['show_order']=true;
$options['order']='sort';
$options['group_id']=$Main->GPC['group_id'];
$options['type']='repeater';
$fields=$SettingsClass->GetSettingsFields($options,'all');
$fields=$SettingsClass->MakeFieldsTree(0,$fields);
$fields=$SettingsClass->MakeFieldsTreeSelect($fields,$data_info['cs_parent_id']);

$Main->template->Display(array(
        'info'=>$data_info,
        'edit'=>$edit,
        'types'=>$types,
        'fields'=>$fields
    )
);
