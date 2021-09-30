<?php

if (!$Main->user_info['user_id']){
    SiteRedirect('/login/');
}

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title'=>'Кабинет',
);

$page_name='Кабинет';
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
        'background'=>BASE_URL.'/assets/images/static/cabinet_bg.jpg',

    ),
);
$Courses->user_premiums->CreateModel();
$Courses->user_premiums->model->columns_where->getUserId()->setValue($Main->user_info['user_id']);
$prems = $Courses->user_premiums->GetList();
$premiums = [];
$theme_ids = [];

foreach ($prems as $prem){
    $Courses->themes->CreateModel();
    $Courses->themes->model->setSelectField($Courses->themes->model->getTableName().'.*, count(courses_videos.video_id) as videos_count');
    $Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemName('icon'));
    $Courses->themes->model->setJoin('LEFT JOIN courses_videos ON courses_videos.video_theme = courses_themes.theme_id');
    $Courses->themes->model->columns_where->getId()->setValue($prem['premium_theme_id']);
    $theme = $Courses->themes->GetItem();
    if ($theme['theme_id']) {
        $premiums[$theme['theme_id']] = $theme;
        $theme_ids[] = $theme['theme_id'];
    }
}
//print_r(array_keys($premiums));exit;
$Courses->themes->CreateModel();
$Courses->themes->model->setSelectField($Courses->themes->model->getTableName().'.*, courses_user_videos.uv_date_to, count(courses_user_videos.uv_id) as videos_count');
$Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemName('icon'));
$Courses->themes->model->setJoin('LEFT JOIN courses_videos ON courses_videos.video_theme = courses_themes.theme_id');
$Courses->themes->model->setJoin('LEFT JOIN courses_user_videos ON courses_videos.video_id = courses_user_videos.uv_video_id');
$Courses->themes->model->addWhereCustom('courses_user_videos.uv_user_id  = '.$Main->user_info['user_id'].' AND courses_user_videos.uv_date_to >= '.date('Y-m-d', time()).' ');($theme_ids);
$Courses->themes->model->setOrderBy('courses_user_videos.uv_date_to DESC');

$themes = $Courses->themes->GetList();
foreach($themes as $k => $theme){
    if (in_array($theme['theme_id'], $theme_ids)){
        unset($themes[$k]);
    }
}

$page = $Main->template->global_vars['page'];
$Main->template->Display(['themes'=>$themes, 'premiums'=>$premiums]);