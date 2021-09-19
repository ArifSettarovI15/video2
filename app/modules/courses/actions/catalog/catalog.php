<?php

$Main->user->PagePrivacy();

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title'=>'Видеокаталог',
);

$page_name='Видеокаталог';
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'meta'=>'services',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => $breadcrumbs,
        'title' => $page_name,
        'back_url' => BASE_URL.'/',
        'background'=>BASE_URL.'/assets/images/static/blog_bg.jpg',

    ),
);

$page = $Main->template->global_vars['page'];

$Courses->catalog->CreateModel();
$Courses->catalog->model->setSelectField($Courses->catalog->model->getTableName().'.*');
$Courses->catalog->model->columns_where->getStatus()->setValue(1);
$Courses->catalog->model->SetJoinImage('icon', $Courses->catalog->model->GetTableItemName('icon'));
$Courses->catalog->model->setOrderBy($Courses->catalog->model->GetTableItemName('sort'));
$catalog = $Courses->catalog->GetList();

$Main->template->Display(compact($catalog));