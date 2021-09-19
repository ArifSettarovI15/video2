<?php
$Main->user->PagePrivacy('admin');

$photo_input = 'theme_icon';
$photo_input2 = 'theme_icon_bg';
if ($Main->GPC['do'] === 'edit' or $Main->GPC['action'] === 'process_edit') {
    $edit = 1;

    $data_info = $Courses->themes->GetItemById($Main->GPC['id']);

    if (!$data_info) {
        $Main->error->ObjectNotFound();
    }
}


if ($Main->GPC['action'] === 'process_add' or $Main->GPC['action'] === 'process_edit') {
    $Main->input->clean_array_gpc(
        'r',
        [
            $photo_input => TYPE_UINT,
            $photo_input2 => TYPE_UINT,
            'theme_title' => TYPE_STR,
            'theme_desc' => TYPE_STR,
            'theme_catalog_id' => TYPE_UINT,
            'theme_url' => TYPE_STR,
            'theme_video_price' => TYPE_UINT,
            'theme_all_price' => TYPE_UINT,
            'theme_days' => TYPE_UINT,
        ]
    );

    $error = '';
    $array = array();

    $Courses->themes->CreateModel();

    $Courses->themes->model->columns_where->getUrl()->setValue(serialize($Main->GPC['theme_url']));
    $check = $Courses->themes->GetItem();

    if (!$Main->GPC[$photo_input]) {
        $error = 'Выберите фото';
    } elseif ($check['theme_id']) {
        $error = 'Уже есть курс с таким URL';
    } else {

        $Courses->themes->CreateModel();
        $Courses->themes->model->columns_update->getIcon()->setValue($Main->GPC[$photo_input]);
        $Courses->themes->model->columns_update->getIconBg()->setValue($Main->GPC[$photo_input2]);
        $Courses->themes->model->columns_update->getTitle()->setValue($Main->GPC['theme_title']);
        $Courses->themes->model->columns_update->getDesc()->setValue($Main->GPC['theme_desc']);
        $Courses->themes->model->columns_update->getUrl()->setValue($Main->GPC['theme_url']);
        $Courses->themes->model->columns_update->getCatalogId()->setValue($Main->GPC['theme_catalog_id']);
        $Courses->themes->model->columns_update->getVideoPrice()->setValue($Main->GPC['theme_video_price']);
        $Courses->themes->model->columns_update->getAllPrice()->setValue($Main->GPC['theme_all_price']);
        $Courses->themes->model->columns_update->getDays()->setValue($Main->GPC['theme_days']);

        if ($Main->GPC['action'] === 'process_edit') {
            $Courses->themes->model->columns_where->getId()->setValue($Main->GPC['id']);
            $result = $Courses->themes->Update();
            if ($result) {
                $id = $Main->GPC['id'];
                $array['status'] = true;
                $array['text'] = 'Значение успешно обновлено';
            } else {
                $array['text'] = 'Ошибка обновления';
            }
        } else {
            $id = $Courses->themes->Insert();
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
$page_name = $a_name . ' курс';
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
            'file_id' => $data_info['theme_icon'],
            'icon_url' => $data_info['theme_icon_url']
        )
    ),
    'module' => 'courses',
    'show_select_image' => true,
    'multiple' => false,
    'title' => 'Фото',
    'folder' => 'themes'
);
$image_data2 = array(
    'input_name' => $photo_input2,
    'files' => array(
        array(
            'file_id' => $data_info['theme_icon_bg'],
            'icon_url' => $data_info['theme_icon_bg_url']
        )
    ),
    'module' => 'courses',
    'show_select_image' => true,
    'multiple' => false,
    'title' => 'Фото',
    'folder' => 'themes'
);


$Courses->catalog->CreateModel();
$Courses->catalog->model->setOrderBy('catalog_sort');
$catalog = $Courses->catalog->GetList();

$Main->template->Display([
    'info' => $data_info,
    'image_data1' => $image_data1,
    'image_data2' => $image_data2,
    'catalog' => $catalog,
    'edit' => $edit,
]);