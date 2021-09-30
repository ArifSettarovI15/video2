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
$Courses->catalog->model->setSelectField($Courses->catalog->model->getTableName() . '.*');
$Courses->catalog->model->SetJoinImage('icon', $Courses->catalog->model->GetTableItemName('icon'));
$Courses->catalog->model->setJoin('RIGHT JOIN courses_themes ON courses_themes.theme_catalog_id = courses_catalog.catalog_id');
$Courses->catalog->model->setOrderBy($Courses->catalog->model->GetTableItemName('sort'));
$Courses->catalog->model->setGroupBy('courses_catalog.catalog_id');

$catalog = $Courses->catalog->GetList();
$array['catalog'] = $catalog;

if (count($catalog) == 1){
    $Courses->themes->CreateModel();
    $Courses->themes->model->setSelectField($Courses->themes->model->getTableName() . '.*, courses_catalog.*');
    $Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemName('icon'));
    $Courses->themes->model->setJoin('LEFT JOIN courses_catalog ON courses_themes.theme_catalog_id = courses_catalog.catalog_id');
    $Courses->themes->model->setOrderBy($Courses->themes->model->GetTableItemName('sort'));
    $array['themes'] = $Courses->themes->GetList();
}



$Main->template->Display($array);