<?php
$Main->user->PagePrivacy('admin');
if ($Main->GPC['action']=='delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_UINT
    ));
    $status=$SeoClass->DeleteSeo($Main->GPC['object_id']);
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

$breadcrumbs[]=array(
    'title'=>'Seo'
);

$page_name='Seo';

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
    'seo_title' => TYPE_STR,
    'seo_id' => TYPE_UINT,
    'seo_url' => TYPE_BOOL
));
$filter_options=array();
$filter_options['seo_title']=$Main->GPC['seo_title'];
$filter_options['seo_id']=$Main->GPC['seo_id'];
$filter_options['seo_url']=$Main->GPC['seo_url'];
$variables=$filter_options;



$Paging =new ClassPaging($Main,15);
$Paging->data=$SeoClass->GetSeoList($filter_options,$Paging->per_page,$Paging->sql_start);
$Paging->total=$SeoClass->GetSeoListTotal($filter_options);
$Paging->show_per_page=true;
$Paging->Display('seo/manager/seo_table.html.twig',$variables);