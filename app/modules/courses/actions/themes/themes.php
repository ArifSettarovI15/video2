<?php

$Main->user->PagePrivacy();
$data_info = $Courses->catalog->GetItemByUrl($Main->GPC['catalog']);

if (!$data_info['catalog_id']){
    $Main->error->PageNotFound();
}


$page_name=$data_info['catalog_title'];

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title'=>'Видеокаталог',
    'link'=>BASE_URL.'/videocatalog/'
);
$breadcrumbs[] = array(
    'title'=>$page_name,
);
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
        'background'=>$data_info['catalog_icon_bg_url'],

    ),
);

$page = $Main->template->global_vars['page'];

$Courses->themes->CreateModel();
$Courses->themes->model->setSelectField($Courses->themes->model->getTableName().'.*');
$Courses->themes->model->columns_where->getStatus()->setValue(1);
$Courses->themes->model->columns_where->getCatalogId()->setValue($data_info['catalog_id']);
$Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemName('icon'));
$Courses->themes->model->SetJoinImage('icon_bg', $Courses->themes->model->GetTableItemName('icon_bg'));
$Courses->themes->model->setOrderBy($Courses->themes->model->GetTableItemName('sort'));
$themes = $Courses->themes->GetList();

$Main->template->Display(['themes'=>$themes, 'catalog_url' =>$Main->GPC['catalog']]);