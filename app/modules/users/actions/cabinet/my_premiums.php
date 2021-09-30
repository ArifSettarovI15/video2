<?php

if (!$Main->user_info['user_id']) {
    SiteRedirect('/login/');
}

$theme = $Courses->themes->GetItemByUrl($Main->GPC['theme']);
$Courses->user_premiums->CreateModel();
$Courses->user_premiums->model->columns_where->getUserId()->setValue($Main->user_info['user_id']);
$Courses->user_premiums->model->columns_where->getThemeId()->setValue($theme['theme_id']);
$prem = $Courses->user_premiums->GetItem();

if (!$theme['theme_id'] or !$prem['up_id']){
    SiteRedirect(BASE_URL.'/videocatalog/'.$theme['theme_url']);
    exit;
}

if ($Main->GPC['action']=='watch_video'){
    $Main->input->clean_array_gpc('r',['video_id'=>TYPE_UINT]);
    $video = $Courses->videos->GetItemById($Main->GPC['video_id']);

    $html = $Main->template->Render('frontend/components/video_modal/video_modal.twig',['url'=>$video['video_embed_link']]);
    $Main->template->DisplayJson(['status'=>true, 'html'=>$html]);


}
$breadcrumbs = array();
$breadcrumbs[] = array(
    'title' => 'Кабинет',
    'link' => BASE_URL . '/cabinet/'
);
$breadcrumbs[] = array(
    'title' => $theme['theme_title'],
);

$page_name = $theme['theme_title'];
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'meta' => 'services',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => $breadcrumbs,
        'title' => $page_name,
        'back_url' => BASE_URL . '/cabinet/',
        'background' => $theme['theme_icon_bg_url'],

    ),
);


$Courses->videos->CreateModel();
$Courses->videos->model->setSelectField($Courses->videos->model->getTableName() . '.*');
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
        $Courses->videos->model->setSelectField($Courses->videos->model->getTableName() . '.*');
        $Courses->videos->model->SetJoinImage('icon', 'video_icon');
        $Courses->videos->model->columns_where->getTheme()->setValue($theme['theme_id']);
        $Courses->videos->model->columns_where->getBlock()->setValue($block['block_id']);
        $Courses->videos->model->setOrderBy('video_sort');
        $videos = $Courses->videos->GetList();

        if (!count($videos)) {

            unset($blocks[$k]);
        } else {
            $blocks[$k]['videos'] = $videos;
        }
    }
}

$Courses->videos->CreateModel();
$Courses->videos->model->setSelectField($Courses->videos->model->getTableName() . '.*');
$Courses->videos->model->SetJoinImage('icon', 'video_icon');
$Courses->videos->model->columns_where->getBlock()->setValue(0);
$Courses->videos->model->columns_where->getId()->notValue($main_video['video_id']);
$Courses->videos->model->columns_where->getTheme()->setValue($theme['theme_id']);
$Courses->videos->model->setOrderBy('video_sort');
$videos = $Courses->videos->GetList();

//$Courses->themes->CreateModel();
//$Courses->themes->model->setSelectField($Courses->themes->model->getTableName().'.*, courses_user_videos.uv_date_to, count(courses_user_videos.uv_id) as videos_count');
//$Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemName('icon'));
//$Courses->themes->model->setJoin('LEFT JOIN courses_videos ON courses_videos.video_theme = courses_themes.theme_id');
//$Courses->themes->model->setJoin('LEFT JOIN courses_user_videos ON courses_videos.video_id = courses_user_videos.uv_video_id');
//$Courses->themes->model->addWhereCustom('courses_user_videos.uv_user_id  = '.$Main->user_info['user_id'].' AND courses_user_videos.uv_date_to >= '.date('Y-m-d', time()));
//$Courses->themes->model->setGroupBy($Courses->themes->model->GetTableItemName('id'));
//$Courses->themes->model->setOrderBy('courses_user_videos.uv_date_to DESC');
//$themes = $Courses->themes->GetList();


$Main->template->Display(['theme' => $theme, 'videos' => $videos, 'blocks' => $blocks, 'main_video' => $main_video]);