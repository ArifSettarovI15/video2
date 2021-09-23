<?php

$Main->input->clean_array_gpc('r', [
    'user'=>TYPE_STR,
    'confirm'=>TYPE_STR
]);
$user= $Main->user->GetUserByLogin($Main->GPC['user']);
if (md5($user['user_email']) == $Main->GPC['confirm']) {
    if (!$user['user_active']) {
        $Main->user->ActivateAccount($user['user_id']);
        print_r('Аккаунт успешно активирован');
        exit;
    } else {
        print_r('Аккаунт активирован ранее');
        exit;
    }
}else{
    print_r('Такого пользователя не существует');exit;
}