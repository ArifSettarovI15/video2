<?php
global $Courses;
global $Main;
global $SettingsClass;

$Main->user->PagePrivacy();


$Main->template->SetPageAttributes(
    array(
        'title' => 'SNG2',
    ),
    array(
        'background' => BASE_URL . '/assets/images/static/mainpage_bg.jpg',
    )
);


$filer_s = array();
$filer_s['key'] = 'mainpage';
$filter_options['show_order'] = true;
$array['mainpage'] = $SettingsClass->GetGroupValues($filer_s);

$filer_s = array();
$filer_s['key'] = 'about_course';
$filter_options['show_order'] = true;
$array['about_course'] = $SettingsClass->GetGroupValues($filer_s);

$Courses->catalog->CreateModel();
$Courses->catalog->model->setSelectField($Courses->catalog->model->getTableName() . '.*');
$Courses->catalog->model->SetJoinImage('icon', $Courses->catalog->model->GetTableItemName('icon'));
$Courses->catalog->model->setOrderBy($Courses->catalog->model->GetTableItemName('sort'));
$array['catalog'] = $Courses->catalog->GetList();


$filter_options = [];
$filter_options['order'] = 'views';
$filter_options['content_type'] = 'articles';
$filter_options['show_order'] = true;
$filter_options['skip_date'] = true;
$array['articles'] = $ContentClass->GetContentList($filter_options, 9);

$array['is_home'] = true;
$array['faqs'] = $FaqsList->GetActiveFaqs();

if ($Main->GPC['action'] == 'order_payed') {

    $merch = json_decode($_POST['merchant_data']);
    $order = $Courses->orders->SetOrderPayed($merch->order_id);
    if ($order['order_type'] == 'videos') {
        $videos = unserialize($order['order_data']);
        foreach ($videos as $video) {
            $video_db = $Courses->videos->GetItemById($video);
            $theme_db = $Courses->themes->GetItemById($video_db['video_theme']);


            $date = date('Y-m-d', time());
            $date2 = date('Y-m-d', strtotime($date . " + {$theme_db['theme_days']} days"));
            $Courses->user_videos->CreateModel();
            $Courses->user_videos->model->columns_where->getUserId()->setValue($order['order_user_id']);
            $Courses->user_videos->model->columns_where->getVideoId()->setValue($video_db['video_id']);
            $item = $Courses->user_videos->GetItem();

            $Courses->user_videos->CreateModel();
            $Courses->user_videos->model->columns_update->getUserId()->setValue($order['order_user_id']);
            $Courses->user_videos->model->columns_update->getVideoId()->setValue($video_db['video_id']);
            $Courses->user_videos->model->columns_update->getDateTo()->setValue($date2);
            if ($item['uv_id']) {
                $Courses->user_videos->model->columns_where->getId()->setValue($item['uv_id']);
                $Courses->user_videos->Update();
            } else {
                $Courses->user_videos->Insert();
            }


        }
    }
    $array['show_thx'] = 1;
}

$Main->template->Display(
    $array
);
