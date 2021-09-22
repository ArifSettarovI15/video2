<?php


$Main->user->PagePrivacy();



$course = $Courses->catalog->GetItemByUrl($Main->GPC['catalog']);
$theme = $Courses->themes->GetItemByUrl($Main->GPC['theme']);


if (!$course['catalog_id'] or !$theme['theme_id']) {
    $Main->error->PageNotFound();
}



if($Main->GPC['action'] === 'buy_videos'){
    $Main->input->clean_array_gpc('r',['ids'=>TYPE_ARRAY]);
    print_r($Main->GPC['ids']);exit;
}

$page_name = $theme['theme_title'];

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title' => 'Видеокаталог',
    'link' => BASE_URL . '/videocatalog/'
);
$breadcrumbs[] = array(
    'title' => $course['catalog_title'],
    'link' => BASE_URL . '/videocatalog/'.$course['catalog_url'].'/'
);
$breadcrumbs[] = array(
    'title' => $page_name,
);
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'meta' => 'services',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => $breadcrumbs,
        'title' => $page_name,
        'back_url' => BASE_URL . '/',
        'background' => $theme['theme_icon_bg_url'],

    ),
);

$page = $Main->template->global_vars['page'];

$Courses->videos->CreateModel();
$Courses->videos->model->setSelectField($Courses->videos->model->getTableName().'.*');
$Courses->videos->model->SetJoinImage('icon', 'video_icon');
$Courses->videos->model->columns_where->getTheme()->setValue($theme['theme_id']);
$Courses->videos->model->columns_where->getMain()->setValue(1);
$main_video = $Courses->videos->GetItem();

$Courses->blocks->CreateModel();
$Courses->blocks->model->columns_where->getTheme()->setValue($theme['theme_id']);
$Courses->blocks->model->setOrderBy('block_sort');

$blocks = $Courses->blocks->GetList();
if (count($blocks)) {
    foreach ($blocks as $k => $block) {
        $Courses->videos->CreateModel();
        $Courses->videos->model->setSelectField($Courses->videos->model->getTableName().'.*');
        $Courses->videos->model->SetJoinImage('icon', 'video_icon');
        $Courses->videos->model->columns_where->getBlock()->setValue($block['block_id']);
        $Courses->videos->model->setOrderBy('video_sort');
        $videos = $Courses->videos->GetList();
        if (!count($videos)){
            unset($blocks[$k]);
        }else{
            $blocks[$k]['videos'] = $videos;
        }
    }
}

$Courses->videos->CreateModel();
$Courses->videos->model->setSelectField($Courses->videos->model->getTableName().'.*');
$Courses->videos->model->SetJoinImage('icon', 'video_icon');
$Courses->videos->model->columns_where->getBlock()->setValue(0);
$Courses->videos->model->setOrderBy('video_sort');
$videos = $Courses->videos->GetList();


$Main->template->Display(compact('theme', 'main_video','blocks', 'videos'));