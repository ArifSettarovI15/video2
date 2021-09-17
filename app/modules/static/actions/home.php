<?php
global $Courses;
global $Main;
global $SettingsClass;


$Main->user->PagePrivacy();


$Main->template->SetPageAttributes(
    array(
        'title' => 'SNG2'
    )
);


$filer_s = array();
$filer_s['key'] = 'mainpage';
$filter_options['show_order']=true;
$array['mainpage'] = $SettingsClass->GetGroupValues($filer_s);

$filer_s = array();
$filer_s['key'] = 'about_course';
$filter_options['show_order']=true;
$array['about_course'] = $SettingsClass->GetGroupValues($filer_s);

$Courses->catalog->CreateModel();
$Courses->catalog->model->setSelectField($Courses->catalog->model->getTableName().'.*');
$Courses->catalog->model->SetJoinImage('icon', $Courses->catalog->model->GetTableItemName('icon'));
$Courses->catalog->model->setOrderBy($Courses->catalog->model->GetTableItemName('sort'));
$array['catalog'] = $Courses->catalog->GetList();


$filter_options = [];
$filter_options['order']='views';
$filter_options['content_type']='articles';
$filter_options['show_order']=true;
$filter_options['skip_date']=true;
$array['articles'] = $ContentClass->GetContentList($filter_options,9);

$array['is_home'] = true;
$array['faqs'] = $FaqsList->GetActiveFaqs();


$Main->template->Display(
    $array
);
