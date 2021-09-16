<?php

$page_name='Вопрос-Ответ';

$breadcrumbs=array();
$breadcrumbs[]=array(
    'title'=>$page_name
);



$filter_s=array();
$filter_s['key']='faq';
//$filter_s['order']='sort';
$fields= $SettingsClass->GetGroupValues($filter_s);
$fields = "data";
//$articles = $ContentClass->getLastArticles();

$array = array();
$array['fields'] = $fields;
//$array['articles'] = $articles;

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

$Main->template->Display($array);
