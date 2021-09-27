<?php

$Main->user->PagePrivacy();

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title'=>'Контакты',
);

$page_name='Контакты';
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
        'background'=>BASE_URL.'/assets/images/static/mainpage_bg2.jpg',

    ),
);

$filter_s=array();
$filter_s['key']='contacts';
$filter_options['show_order']=true;
$contacts=$SettingsClass->GetGroupValues($filter_s);


$Main->template->Display(compact('contacts'));