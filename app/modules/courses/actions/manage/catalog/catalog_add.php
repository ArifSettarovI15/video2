<?php
$Main->user->PagePrivacy('admin');

$photo_input = 'catalog_icon';
$photo_input2 = 'catalog_icon2';
if ($Main->GPC['do'] === 'edit' or $Main->GPC['action'] === 'process_edit') {
    $edit = 1;

    $data_info = $Courses->catalog->GetItemById($Main->GPC['id']);

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
            'catalog_title' => TYPE_STR,
            'catalog_url' => TYPE_STR,
        ]
    );

    $error = '';
    $array = array();

    $Courses->catalog->CreateModel();

    $Courses->catalog->model->columns_where->getUrl()->setValue(serialize($Main->GPC['catalog_url']));
    $check = $Courses->catalog->GetItem();

    if (!$Main->GPC[$photo_input] and !$edit) {
        $error = 'Выберите фото';
    } elseif ($check['catalog_id'] and !$edit) {
        $error = 'Уже есть направление с таким URL';
    } else {

        $Courses->catalog->CreateModel();
        $Courses->catalog->model->columns_update->getIcon()->setValue($Main->GPC[$photo_input]);
        $Courses->catalog->model->columns_update->getIconBg()->setValue($Main->GPC[$photo_input2]);
        $Courses->catalog->model->columns_update->getTitle()->setValue($Main->GPC['catalog_title']);
        $Courses->catalog->model->columns_update->getUrl()->setValue($Main->GPC['catalog_url']);

        if ($Main->GPC['action'] === 'process_edit') {
            $Courses->catalog->model->columns_where->getId()->setValue($Main->GPC['id']);
            $result = $Courses->catalog->Update();
            if ($result) {
                $id = $Main->GPC['id'];
                $array['status'] = true;
                $array['text'] = 'Значение успешно обновлено';
            } else {
                $array['text'] = 'Ошибка обновления';
            }
        } else {
            $id = $Courses->catalog->Insert();
            $array['text'] = 'Значение успешно добавлено';
            $array['status'] = true;
        }
        $Main->files->AddFileIdsItems('catalog', $id, $Main->GPC[$photo_input]);
        $Main->files->UpdateFileIdsItemsSort('catalog', $id, $Main->GPC[$photo_input]);
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
$page_name = $a_name . ' направление';
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'keywords' => '',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => array(
            array(
                'title' => 'Видеокаталог',
                'link' => BASE_URL . '/manager/courses/catalog/'
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
            'file_id' => $data_info['catalog_icon'],
            'icon_url' => $data_info['catalog_icon_url']
        )
    ),
    'module' => 'courses',
    'show_select_image' => true,
    'multiple' => false,
    'title' => 'Фото',
    'folder' => 'catalog'
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

$Main->template->Display([
    'info' => $data_info,
    'image_data1' => $image_data1,
    'image_data2' => $image_data2,
    'edit' => $edit,
]);


