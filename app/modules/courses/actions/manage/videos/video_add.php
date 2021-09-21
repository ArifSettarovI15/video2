<?php

$theme = $Courses->themes->GetItemById($Main->GPC['theme']);

$photo_input = 'video_icon';

if ($Main->GPC['do'] === 'edit' or $Main->GPC['action'] === 'process_edit') {
    $edit = 1;

    $data_info = $Courses->videos->GetItemById($Main->GPC['block']);

    if (!$data_info) {
        $Main->error->ObjectNotFound();
    }
}


if ($Main->GPC['action'] === 'process_add' or $Main->GPC['action'] === 'process_edit') {
    $Main->input->clean_array_gpc(
        'r',
        [
            'video_title' => TYPE_STR,
            'video_desc' => TYPE_STR,
            'video_embed_link' => TYPE_STR,
            $photo_input => TYPE_UINT,
            'video_block' => TYPE_UINT,
            'video_price' => TYPE_UINT,
            'video_use_sale' => TYPE_BOOL,
            'video_main' => TYPE_BOOL,
        ]
    );

    $error = '';
    $array = array();

    $Courses->videos->CreateModel();

    $Courses->videos->model->columns_where->getTitle()->setValue(trim($Main->GPC['block_title']));
    $check = $Courses->videos->GetItem();
    $Courses->videos->CreateModel();

    $Courses->videos->model->columns_where->getEmbedLink()->setValue(trim($Main->GPC['video_embed_link']));
    $check2 = $Courses->videos->GetItem();

    if ($check['video_id'] and !$edit) {
        $error = 'Уже есть блок с таким названием';
    }
    elseif ($check2['video_id'] and !$edit) {
        $error = 'Уже есть блок с такой ссылкой';
    }
    else {

        $Courses->videos->CreateModel();
        $Courses->videos->model->columns_update->getTitle()->setValue($Main->GPC['video_title']);
        $Courses->videos->model->columns_update->getDesc()->setValue($Main->GPC['video_desc']);
        $Courses->videos->model->columns_update->getTheme()->setValue($Main->GPC['theme']);
        $Courses->videos->model->columns_update->getEmbedLink()->setValue($Main->GPC['video_embed_link']);
        $Courses->videos->model->columns_update->getIcon()->setValue($Main->GPC[$photo_input]);
        $Courses->videos->model->columns_update->getBlock()->setValue($Main->GPC['video_block']);
        $Courses->videos->model->columns_update->getPrice()->setValue($Main->GPC['video_price']);
        $Courses->videos->model->columns_update->getUseSail()->setValue($Main->GPC['video_use_sale']);
        $Courses->videos->model->columns_update->getMain()->setValue($Main->GPC['video_main']);

        if ($Main->GPC['action'] === 'process_edit') {
            $Courses->videos->model->columns_where->getId()->setValue($Main->GPC['id']);
            $result = $Courses->videos->Update();
            if ($result) {
                $id = $Main->GPC['id'];
                $array['status'] = true;
                $array['text'] = 'Значение успешно обновлено';
            } else {
                $array['text'] = 'Ошибка обновления';
            }
        } else {
            $id = $Courses->videos->Insert();
            $array['text'] = 'Значение успешно добавлено';
            $array['status'] = true;
        }
    }
    if ($error != '') {
        $array['status'] = false;
        $array['text'] = $error;
    } else {
        $array['status'] = true;
    }
    $Main->template->DisplayJson($array);

}

if ($edit == 1) {
    $a_name = 'Редактировать';
} else {
    $a_name = 'Добавить';
}
$page_name = $a_name . ' видео';
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'keywords' => '',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => array(
            array(
                'title' => 'Курсы',
                'link' => BASE_URL . '/manager/courses/themes/'
            ),
            array(
                'title' => $theme['theme_title'],
                'link' => BASE_URL . '/manager/courses/themes/edit/'.$theme['theme_id']
            ),
            array(
                'title' => 'Видео',
                'link' => BASE_URL . '/manager/courses/themes/'.$theme['theme_id'].'/videos'
            ),

            array(
                'title' => $a_name
            ),
        ),
        'title' => $page_name
    )
);

$image_data1 = array(
    'input_name' => $photo_input,
    'files' => array(
        array(
            'file_id' => $data_info['video_icon'],
            'icon_url' => $data_info['video_icon_url']
        )
    ),
    'module' => 'courses',
    'show_select_image' => true,
    'multiple' => false,
    'title' => 'Фото',
    'folder' => 'videos'
);

$Courses->blocks->CreateModel();
$Courses->blocks->model->columns_where->getTheme()->setValue($theme['theme_id']);
$blocks = $Courses->blocks->GetList();

$Main->template->Display([
    'image_data1' => $image_data1,
    'info' => $data_info,
    'theme' => $theme,
    'blocks' => $blocks,
    'edit' => $edit,
]);