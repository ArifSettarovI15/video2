<?php
if (!$Main->user_info['user_id'])
$Main->user->PagePrivacy('admin');

if ($Main->GPC['action']=='delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_UINT
    ));
    $array = array();
    $info=$RouteAdminClass->GetRouteInfo($Main->GPC['object_id']);
    $error='';

    if ($info) {
        $array['list']=array();
        $list=$RouteAdminClass->GetChildrenRoutes($info['id']);
        foreach ($list as $el) {
            $el_id=$el['id'];
            if ($RouteAdminClass->DeleteRoute($el_id)==false) {
                $error=$Main->lang->data['routes']['routing_delete_error'];
            }
            $array['list'][]=$el_id;
        }

        if ($RouteAdminClass->DeleteRoute($Main->GPC['object_id'])==false) {
            $array['list'][] = $Main->GPC['object_id'];
        }
 
        if ($error=='') {
            $array['status']=true;
            $array['text']=$Main->lang->data['routes']['routing_delete_ok'];
        }
        else {
            $array['status']=false;
            $array['text']=$error;
        }
    }
    else {
        $array['status']=false;
        $array['text']=$Main->lang->data['routes']['route_not_exist'];
    }
    $Main->template->DisplayJson($array);
}
if ($Main->GPC['action']=='UpdateSort') {
    $Main->input->clean_array_gpc('r', array(
        'tree' => TYPE_ARRAY
    ));
    $array = array();
    if ( $RouteAdminClass->ProcessRoutesSort($Main->GPC['tree'],0,0)) {
        $array['status']=true;
        $array['text']=$Main->lang->data['routes']['routing_sort_ok'];
    }
    else {
        $array['status']=false;
        $array['text']=$Main->lang->data['routes']['routing_sort_error'];
    }
    $Main->template->DisplayJson($array);
}


$list=$RouteAdminClass->GetRoutes();

if ($Main->GPC['ajax']==1) {

    $array=array();
    $array['status']=true;
    $Main->template->DisplayJson($array);
}

$Main->template->Display(
    array(
        'page_name'=>$Main->lang->data['routes']['routing'],
        'routes_list' => $list
    )
);
