<?php
$Main->user->PagePrivacy('user,admin');

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

$page = $Main->template->global_vars['page'];
$Main->template->Display();