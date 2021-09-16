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
    $data_info=$SettingsClass->GetGroupSetting($Main->GPC['id']);
    if ($data_info) {

    }
    else {
        $Main->error->ObjectNotFound();
    }
}

if ($Main->GPC['action']=='process_add'  OR $Main->GPC['action']=='process_edit') {
    $Main->input->clean_array_gpc('r', array(
        'title' => TYPE_STR,
        'key' => TYPE_STR,
        'status' => TYPE_BOOL,
        'id'=>TYPE_UINT
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
    else{
        $info = $SettingsClass->GetGroupSettingsByTitle($Main->GPC['title']);
        if ($info && $info['cs_group_id'] != $Main->GPC['id']) {
            $error = 'Такая группа уже существует';
            $array['error_field'] = 'title';
        } else {
            $info = $SettingsClass->GetGroupSettingsByKey($Main->GPC['key']);
            if ($info && $info['cs_group_id'] != $Main->GPC['id']) {
                $error = 'Такой ключ уже существует';
                $array['error_field'] = 'key';
            }
            else {
                if ($Main->GPC['action'] == 'process_edit') {
                    $id = $Main->GPC['cat_id'];
                    if ($SettingsClass->UpdateGroupSettings($Main->GPC['id'], $Main->GPC['title'],$Main->GPC['key'], $Main->GPC['status'])) {
                        $array['status'] = true;
                        $array['text'] = 'Значение успешно обновлено';
                    } else {
                        $array['text'] = 'Ошибка обновления';
                    }

                } else {

                    $id = $SettingsClass->AddGroupSettings($Main->GPC['title'], $Main->GPC['key'],$Main->GPC['status']);

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

$page_name=$a_name.' группу';
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
                'title'=>$a_name
            ),
        ),
        'title'=>$page_name
    )
);

$Main->template->Display(array(
        'info'=>$data_info,
        'edit'=>$edit
    )
);