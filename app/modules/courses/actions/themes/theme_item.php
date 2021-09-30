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
        $price_without_sale = 0;
        $price = 0;
        $price7 = 0;
        $price14 = 0;
        $price21 = 0;
        $price28 = 0;
        $price35 = 0;
        $count = 0;

        foreach ($Main->GPC['ids'] as $id) {

            $video = $Courses->videos->GetItemById($id);

            if ($video['video_use_sale']) {
                ++$count;
                if ($count < 11) {
                    $price += (int)$video['video_price'];
                } elseif (11 <= $count && $count < 21) {
                    $price7 += (int)$video['video_price'];
                } elseif (21 <= $count && $count < 31) {
                    $price14 += (int)$video['video_price'];
                } elseif (31 <= $count && $count < 41) {
                    $price21 += (int)$video['video_price'];
                } elseif (41 <= $count && $count < 51) {
                    $price28 += (int)$video['video_price'];
                } elseif (51 <= $count) {
                    $price35 += (int)$video['video_price'];
                }
            } else {
                $price_without_sale += (int)$video['video_price'];;
            }
        }
        if ($price7 > 0) {
            $price7 = $price7 / 100 * 93;
        }
        if ($price14 > 0) {
            $price14 = $price14 / 100 * 86;
        }
        if ($price21 > 0) {
            $price21 = $price21 / 100 * 79;
        }
        if ($price28 > 0) {
            $price28 = $price28 / 100 * 72;
        }
        if ($price35 > 0) {
            $price35 = $price35 / 100 * 65;
        }

        $total_price = (int)$price + (int)$price7 + (int)$price14 + (int)$price21 + (int)$price28 + (int)$price35+(int)$price_without_sale;

        $Courses->orders->CreateModel();
        $Courses->orders->model->columns_update->getAmount()->setValue($total_price);
        $Courses->orders->model->columns_update->getUserId()->setValue($Main->user_info['user_id']);
        $Courses->orders->model->columns_update->getData()->setValue(serialize($Main->GPC['ids']));
        $Courses->orders->model->columns_update->getType()->setValue('videos');
        $order_id = $Courses->orders->Insert();

        $data = $Courses->orders->CheckoutUrl($total_price, $order_id);

        $Main->template->DisplayJson(['status' => true, 'data' => $data]);
    }
    exit;
}


if ($Main->GPC['action'] === 'buy_premium') {


    if (!$Main->user_info['user_id']) {
        $html = $Main->template->Render('frontend/components/login_modal/login_modal.twig', ['register' => 1, 'payment' => true, 'inline' => true]);
        $Main->template->DisplayJson(['status' => true, 'login' => true, 'html' => $html]);
    } else {

        $theme = $Courses->themes->GetItemByUrl($Main->GPC['theme']);
        $Courses->orders->CreateModel();
        $Courses->orders->model->columns_update->getAmount()->setValue($theme['theme_all_price']);
        $Courses->orders->model->columns_update->getUserId()->setValue($Main->user_info['user_id']);
        $Courses->orders->model->columns_update->getData()->setValue($theme['theme_id']);
        $Courses->orders->model->columns_update->getType()->setValue('premium');
        $order_id = $Courses->orders->Insert();

        $data = $Courses->orders->CheckoutUrl($theme['theme_all_price'], $order_id, 'premium_payed');

        $Main->template->DisplayJson(['status' => true, 'data' => $data]);

        exit;
    }
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