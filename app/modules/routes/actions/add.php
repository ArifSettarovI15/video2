<?php

$Main->user->PagePrivacy('admin');

if ($Main->GPC['action']=='GetTemplates') {
    $Main->input->clean_array_gpc('r', array(
        'module' => TYPE_STR
    ));
    $array=array();
    if ($Main->GPC['module']!=''){
        $array['list']=$RouteAdminClass->GetModulesTemplatesList($Main->GPC['module']);
        $array['status']=true;
    }
    else {
        $array['text']=$Main->lang->data['routes']['get_templates_error'];
        $array['status']=false;
    }

    $Main->template->DisplayJson($array);
}
if ($Main->GPC['action']=='GetActions') {
    $Main->input->clean_array_gpc('r', array(
        'module' => TYPE_STR
    ));
    $array=array();
    if ($Main->GPC['module']!=''){
        $array['list']=$RouteAdminClass->GetModulesActionsList($Main->GPC['module']);
        $array['status']=true;
    }
    else {
        $array['text']=$Main->lang->data['routes']['get_actions_error'];
        $array['status']=false;
    }

    $Main->template->DisplayJson($array);
}


$edit=0;
$info=array();
$parent_info=array();

if ($Main->GPC['action']=='process_add' OR $Main->GPC['action']=='process_edit') {
    $Main->input->clean_array_gpc('r', array(
        'module' => TYPE_STR,
        'template' => TYPE_STR,
        'regexp_value'=> TYPE_STR,
        'route_action'=> TYPE_STR,
        'rules' => TYPE_NOCLEAN,
        'parent_id' => TYPE_UINT
    ));

    $error='';
    $error_field='';
    $mes='';
    $rules=@json_decode($Main->GPC['rules']);
    if ($Main->GPC['module']=='') {
        $error=$Main->lang->data['routes']['select_module'];
        $error_field='module';
    }
    elseif ($rules==false) {
        $error=$Main->lang->data['routes']['error_with_rules'];
    }
    elseif (count((array)$rules)==0) {
        $error=$Main->lang->data['routes']['add_rules'];
    }
    else {
        $parent_regexp='';
        $level=0;

        if ($Main->GPC['parent_id']) {
            $parent_info=$RouteAdminClass->GetRouteInfo($Main->GPC['parent_id']);
            if ($parent_info) {
                $parent_regexp=$parent_info['regexp_value'];
                $level=$parent_info['level']+1;
            }
        }

        $regexp_keys_array=array();
       
        if ($parent_regexp!='') {
            $regexp_keys_array[]=$parent_regexp;
        }
        $values_keys_array=array();



        foreach ($rules as $rule) {
            if ($rule->rule!='') {
                $regexp_keys_array[] = $rule->rule;
                $values_keys_array[] = $rule->rule;
            }
        }
        $regexp_value=implode('/',$regexp_keys_array);
        $value=implode('/',$values_keys_array);

        $rules=objectToArray($rules);
        /*$new_rules=array();
        foreach ( $parent_info['rules'] as $rule){
            if ($rule['static']==0) {
                $new_rules[] = $rule;
            }
        }
        $rules=$new_rules+$rules;*/
        $data=$RouteAdminClass->GetRoutesByRegexp($regexp_value);

        if ( $data && $Main->GPC['id']!=$data['id']) {
            $error=$Main->lang->data['routes']['route_already_exist'];
        }
        else {
            if ($Main->GPC['action']=='process_edit') {
                $RouteAdminClass->UpdateRoute(
                $Main->GPC['id'],
                    $regexp_value,
                    $value,
                $Main->GPC['module'],
                $Main->GPC['route_action'],
                    $rules,
                    $Main->GPC['template'],
                $Main->GPC['parent_id'],
                    $level);

                $RouteAdminClass->UpdateChildRegexp($Main->GPC['id']);
                $mes = $Main->lang->data['routes']['route_update_ok'];
            }
            else {
                $RouteAdminClass->AddRoute(
                    $regexp_value,
                    $value,
                    $Main->GPC['module'],
                    $Main->GPC['route_action'],
                    $rules,
                    $Main->GPC['template'],
                    $Main->GPC['parent_id'],
                    $level);
                $mes = $Main->lang->data['routes']['route_added_ok'];
            }
        }
    }

    if ($error=='') {
        $status=true;
        $text=$mes;
    }
    else {
        $status=false;
        $text=$error;
    }


    $array_json=array(
        'status'=>$status,
        'text'=>$text,
        'error_field'=>$error_field
    );

    $Main->template->DisplayJson($array_json);
}


if($Main->GPC['do']=='edit'){
    $edit=1;
    $info=$RouteAdminClass->GetRouteInfo($Main->GPC['id']);
    if (intval($info['id'])==0) {
        $Main->error->PageNotFound();
    }

    $parent_info=$RouteAdminClass->GetRouteInfo($info['parent_id']);
    $info['routes_list']=$RouteAdminClass->GetModulesTemplatesList($info['module']);
    $info['actions_list']=$RouteAdminClass->GetModulesActionsList($info['module']);
}

$list=$RouteAdminClass->GetRoutes();
$modules_list=$RouteAdminClass->GetModulesList();

$Main->template->Display(
    array(
        'routes_list'=>$list,
        'modules_list'=>$modules_list,
        'edit'=>$edit,
        'info'=>$info,
        'parent_info'=>$parent_info
    )
);