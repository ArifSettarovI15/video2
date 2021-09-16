<?php
$Main->user->PagePrivacy('admin');
$data_values=array();
$setting_info=$SettingsClass->GetSetting($Main->GPC['parent_id']);
$edit=0;
if ($setting_info) {

}
else {
    $Main->error->ObjectNotFound();
}


if ($Main->GPC['do']=='edit' OR $Main->GPC['action']=='process_edit') {
    $edit=1;
    $ff=array();
    $ff['key']=$setting_info['cs_group_key'];
    $ff['parent_key']=$setting_info['cs_key'];
    $ff['line_key']=$Main->GPC['line_key'];
    $data_values=$SettingsClass->GetGroupValues($ff,0,1);
}

$page_name=$setting_info['cs_title'];
$breadcrumbs=array();
$breadcrumbs[]=array(
    'title'=>'Группы полей',
    'link'=>BASE_URL.'/manager/settings/'
);
$breadcrumbs[]=array(
    'title'=>$setting_info['cs_group_name'],
    'link'=>BASE_URL.'/manager/settings/view/'.$setting_info['cs_group_id'].'/'
);
$breadcrumbs[]=array(
    'title'=>$setting_info['cs_title']
);



$options=array();
$options['show_order']=true;
$options['order']='sort';
$options['show_order']=true;
$options['parent_id']=$setting_info['cs_id'];
$fields=$SettingsClass->GetSettingsFields($options,'all');


$raw_fields=$fields;
$fields=$SettingsClass->MakeFieldsTree(0,$fields);
$fields['values']=$data_values;
$fields = $SettingsClass->MakeFieldsTreeInputs($fields,'',$setting_info['cs_level']+1,$setting_info['cs_id']);


$data_info=array();

if ($Main->GPC['action']=='process_edit' && $Main->GPC['do']!='edit') {
    $Main->input->clean_array_gpc('r', array(
        'id' => TYPE_UINT
    ));
}


$error='';
if ($Main->GPC['action']=='process_add'  OR $Main->GPC['action']=='process_edit') {
    $Main->input->clean_array_gpc('r', array(
        'id'=>TYPE_UINT
    ));

    $values = array();
    if ($Main->GPC['action']=='process_add') {
        $line_key = GenerateName();
    }
    else {
        $line_key = $Main->GPC['line_key'];
    }
    $error_array=array();

    foreach ($raw_fields as $c_data) {
        $field_data=$SettingsClass->GetSettingsByKey2($setting_info['cs_group_id'], $c_data['cs_key']);

        if ($field_data['cs_type']=='image') {
            $Main->input->clean_array_gpc('r', array(
                $c_data['cs_key'] => TYPE_UINT
            ));
        }
        else {
            $Main->input->clean_array_gpc('r', array(
                $c_data['cs_key'] => TYPE_STR
            ));
        }
        $values[$c_data['cs_key']] = $Main->GPC[$c_data['cs_key']];
        if ($field_data) {
            if ($field_data['cs_required'] && $values[$field_data['cs_key']]=='') {
                $error='Заполните все поля';
                $error_array[]=$field_data['cs_key'];
            }

            if ($error=='') {
                $field_value = $SettingsClass->GetSettingsValue($field_data['cs_id'], $line_key);
                if ($field_value) {
                    $array['text'] = 'Значение обновлено';
                    $SettingsClass->UpdateSettingsValue($field_data['cs_id'], $values[$field_data['cs_key']], $line_key);
                } else {
                    $array['text'] = 'Значение добавлено';
                    $SettingsClass->AddSettingsValue($field_data['cs_id'], $values[$field_data['cs_key']], $line_key);
                }
            }
        }
    }
    $array['error_field']=implode(',',$error_array );
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
$breadcrumbs[]=array(
  'title'=>$a_name
);

$page_name=$a_name.' значение';
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
$Main->template->Display(array(
        'edit'=>$edit,
        'fields'=>$fields
    )
);
