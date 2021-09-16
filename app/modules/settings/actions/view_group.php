<?php
$Main->user->PagePrivacy('admin');
if ($Main->GPC['action']=='sort_table') {
    $Main->input->clean_array_gpc('r', array(
        'data_sort' => TYPE_ARRAY_STR,
        'sort_id'=>TYPE_UINT
    ));

    $pos=0;
    foreach ($Main->GPC['data_sort'] as $line_key) {
        $SettingsClass->UpdateSettingsSort($line_key,$pos);
        $pos++;
    }

    $array['status']=true;
    $array['text']='Позиции обновлены';
    $Main->template->DisplayJson($array);
}

if ($Main->GPC['action']=='delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_STR
    ));
    $status=$SettingsClass->DeleteSettingsValueByLine($Main->GPC['object_id']);
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

$group_info=$SettingsClass->GetGroupSetting($Main->GPC['group_id']);
if ($group_info) {

}
else {
    $Main->error->ObjectNotFound();
}

$options=array();
$options['show_order']=true;
$options['order']='sort';
$options['group_id']=$Main->GPC['group_id'];
$fields=$SettingsClass->GetSettingsFields($options,'all');
$fields=$SettingsClass->MakeFieldsTree(0,$fields);


$error='';
$filter_options=array();
$filter_options['cat_title']=$Main->GPC['cat_title'];

if ( $Main->GPC['action']=='process_edit') {

    $values = array();
    foreach ($fields['levels'][1][0] as $c_data) {
        $Main->input->clean_array_gpc('r', array(
            $c_data['cs_key']=>TYPE_STR
        ));
        $values[$c_data['cs_key']] = $Main->GPC[$c_data['cs_key']];
        $field_data=$SettingsClass->GetSettingsByKey2($c_data['cs_group_id'],$c_data['cs_key']);

        if ($field_data) {
            if ($field_data['cs_type']=='text_input' OR $field_data['cs_type']=='text_area' OR $field_data['cs_type']=='text_rich' OR $field_data['cs_type']=='image') {
                $field_value=$SettingsClass->GetSettingsValue($field_data['cs_id']);
                if ($field_value) {
                    $SettingsClass->DeleteSettingsValue($field_data['cs_id']);
                }

                $SettingsClass->AddSettingsValue($field_data['cs_id'],$values[$field_data['cs_key']]);

            }
        }
    }
    if ($error!='') {
        $array['status']=false;
        $array['text']=$error;
    }
    else {
        $array['text'] = 'Данные обновлены';
        $array['status']=true;
    }
    $Main->template->DisplayJson($array);
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

$filter_s=array();
$filter_s['show_order']=true;
$filter_s['key']=$group_info['cs_group_key'];
$fields_values=$SettingsClass->GetGroupValues($filter_s,0);

$fields['values']=$fields_values;
$fields = $SettingsClass->MakeFieldsTreeInputs($fields);

$Main->template->Display(array(
        'info'=>$group_info,
        'edit'=>$edit,
        'fields'=>$fields,
        'fields_values'=>$fields_values
    )
);
