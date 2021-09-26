<?php


$Main->user->PagePrivacy();


$course = $Courses->catalog->GetItemByUrl($Main->GPC['catalog']);
$theme = $Courses->themes->GetItemByUrl($Main->GPC['theme']);


if (!$course['catalog_id'] or !$theme['theme_id']) {
    $Main->error->PageNotFound();
}


if ($Main->GPC['action'] === 'buy_videos') {


    $Main->input->clean_array_gpc('r', ['ids' => TYPE_ARRAY]);
    if (!$Main->user_info['user_id']) {
        $html = $Main->template->Render('frontend/components/login_modal/login_modal.twig', ['register' => 1, 'payment' => true, 'inline' => true]);
        $Main->template->DisplayJson(['status' => true, 'login' => true, 'html' => $html]);
    } else {
        $for_sale_price = 0;
        $without_sale_price = 0;
        $for_sale_count = 0;
        $price = 0;
        foreach ($Main->GPC['ids'] as $id) {
            $video = $Courses->videos->GetItemById($id);
            if ($video['video_use_sale']) {
                $for_sale_price += (int)$video['video_price'];
                $for_sale_count++;
            } else {
                $without_sale_price += (int)$video['video_price'];;
            }
            $price += (int)$video['video_price'];
        }
        if ($for_sale_count < 11) {
            $total_price = $price;
        } else if ($for_sale_count >= 11 && $for_sale_count < 21) {
            $total_price = $without_sale_price + ($for_sale_price / 100 * 93);
        } else if ($for_sale_count >= 21 && $for_sale_count < 31) {
            $total_price = $without_sale_price + ($for_sale_price / 100 * 86);
        } else if ($for_sale_count >= 31 && $for_sale_count < 41) {
            $total_price = $without_sale_price + ($for_sale_price / 100 * 79);
        } else if ($for_sale_count >= 41 && $for_sale_count < 51) {
            $total_price = $without_sale_price + ($for_sale_price / 100 * 72);
        } else {
            $total_price = $without_sale_price + ($for_sale_price / 100 * 65);
        }
        $Courses->orders->CreateModel();
        $Courses->orders->model->columns_update->getAmount()->setValue($total_price);
        $Courses->orders->model->columns_update->getUserId()->setValue($Main->user_info['user_id']);
        $Courses->orders->model->columns_update->getData()->setValue(serialize($Main->GPC['ids']));
        $Courses->orders->model->columns_update->getType()->setValue('videos');
        $order_id = $Courses->orders->Insert();

        $data = $Courses->orders->CheckoutUrl($total_price, $order_id);

        $Main->template->DisplayJson(['status'=>true, 'data'=>$data]);
    }
    exit;
}

$page_name = $theme['theme_title'];

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title' => 'Видеокаталог',
    'link' => BASE_URL . '/videocatalog/'
);
$breadcrumbs[] = array(
    'title' => $course['catalog_title'],
    'link' => BASE_URL . '/videocatalog/' . $course['catalog_url'] . '/'
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

$Main->template->Display(compact('theme', 'main_video', 'blocks', 'videos'));