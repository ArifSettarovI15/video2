<?php

$Main->user->PagePrivacy();

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title'=>'О нас',
);

$page_name='О нас';
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'meta'=>'about',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => $breadcrumbs,
        'title' => $page_name,
        'back_url' => BASE_URL.'/',
        'background'=>BASE_URL.'/assets/images/static/blog_bg.jpg',

    ),
);

$filter_s=array();
$filter_s['key']='company';
$filter_options['show_order']=true;
$company=$SettingsClass->GetGroupValues($filter_s);


$Main->template->Display(compact('company'));