<?php
$Main->user->PagePrivacy('user,admin');
$Main->user->LogOut($Main->user_info['user_id']);

if ($Main->GPC['ajax']) {
    $html= $Main->template->Render('parts/auth.html.twig');
    $array_json = array(
        'status' => true,
    );

    $Main->template->DisplayJson($array_json);
}
else {
    SiteRedirect();
}
