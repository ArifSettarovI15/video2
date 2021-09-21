<?php

$theme = $Courses->themes->GetItemById($Main->GPC['theme']);


if ($Main->GPC['do'] === 'edit' or $Main->GPC['action'] === 'process_edit') {
    $edit = 1;

    $data_info = $Courses->block->GetItemById($Main->GPC['block']);

    if (!$data_info) {
        $Main->error->ObjectNotFound();
    }
}


if ($Main->GPC['action'] === 'process_add' or $Main->GPC['action'] === 'process_edit') {
    $Main->input->clean_array_gpc(
        'r',
        [
            'block_title' => TYPE_STR,
        ]
    );

    $error = '';
    $array = array();

    $Courses->blocks->CreateModel();

    $Courses->blocks->model->columns_where->getTitle()->setValue(trim($Main->GPC['block_title']));
    $check = $Courses->blocks->GetItem();

    if ($check['block_id']) {
        $error = 'Уже есть блок с таким названием';
    } else {

        $Courses->blocks->CreateModel();
        $Courses->blocks->model->columns_update->getTitle()->setValue($Main->GPC['block_title']);
        $Courses->blocks->model->columns_update->getTheme()->setValue($Main->GPC['theme']);

        if ($Main->GPC['action'] === 'process_edit') {
            $Courses->blocks->model->columns_where->getId()->setValue($Main->GPC['id']);
            $result = $Courses->blocks->Update();
            if ($result) {
                $id = $Main->GPC['id'];
                $array['status'] = true;
                $array['text'] = 'Значение успешно обновлено';
            } else {
                $array['text'] = 'Ошибка обновления';
            }
        } else {
            $id = $Courses->blocks->Insert();
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
$page_name = $a_name . ' блок';
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
                'title' => 'Блоки',
                'link' => BASE_URL . '/manager/courses/themes/'.$theme['theme_id'].'/blocks'
            ),

            array(
                'title' => $a_name
            ),
        ),
        'title' => $page_name
    )
);


$Main->template->Display([
    'info' => $data_info,
    'theme' => $theme,
    'edit' => $edit,
]);