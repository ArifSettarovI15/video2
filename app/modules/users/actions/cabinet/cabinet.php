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

$Courses->themes->CreateModel();
$Courses->themes->model->setSelectField($Courses->themes->model->getTableName().'.*, courses_user_videos.uv_date_to, count(courses_user_videos.uv_id) as videos_count');
$Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemName('icon'));
$Courses->themes->model->setJoin('LEFT JOIN courses_videos ON courses_videos.video_theme = courses_themes.theme_id');
$Courses->themes->model->setJoin('LEFT JOIN courses_user_videos ON courses_videos.video_id = courses_user_videos.uv_video_id');
$Courses->themes->model->addWhereCustom('courses_user_videos.uv_user_id  = '.$Main->user_info['user_id'].' AND courses_user_videos.uv_date_to >= '.date('Y-m-d', time()));
$Courses->themes->model->setGroupBy($Courses->themes->model->GetTableItemName('id'));
$Courses->themes->model->setOrderBy('courses_user_videos.uv_date_to DESC');
$themes = $Courses->themes->GetList();

$page = $Main->template->global_vars['page'];
$Main->template->Display(['themes'=>$themes]);