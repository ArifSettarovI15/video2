<?php
include_once ('ru/ajax.php');

$LANG_ARRAY['routes']=
    array(
        'routing'=>'Роутинг',
        'routing_sort_ok'=>'Порядок роутов изменен',
        'routing_sort_error'=>'Ошибка сортировки роутов',
        'route_not_exist'=>'Такой роутер не существует',
        'routing_delete_ok'=>'Роутер успешно удален',
        'routing_delete_error'=>'Ошибка удаления роутера',
        'module'=>'Модуль',
        'select_module'=>'Выберите модуль',
        'select_action'=>'Выберите action',
        'action'=>'Action',
        'without_action'=>'Без action',
        'select_module_before'=>'Выберите сначало модуль',
        'template'=>'Шаблон',
        'without_template'=>'Без шаблона',
        'regexp'=>'Regexp',
        'rule'=>'Правило',
        'request_name'=>'$_REQUEST["name"]',
        'request_value'=>'Значение',
        'pos'=>'Позиция',
        'rule_static'=>'Статический',
        'rule_dynamic'=>'Динамический',
        'select_rule'=>'Выберите правило',
        'select_type'=>'Выберите тип',
        'get_templates_error'=>'Ошибка получения списка шаблонов',
        'get_actions_error'=>'Ошибка получения списка шаблонов',
        'add_route'=>'Добавить роут',
        'error_with_rules'=>'Ошибка с правилами',
        'add_rules'=>'Добавьте правила',
        'parent'=>'Родительский роут',
        'without_parent'=>'Без парента',
        'route_already_exist'=>'Такой роут уже существует',
        'route_added_ok'=>'Роут успешно добавлен',
        'route_update_ok'=>'Роут успешно обновлен',
        'child_regexp_error'=>'Возникла ошибка regexp дочерних элементов'
    );
$LANG_JS_ARRAY['routes']=array(
    'without_template'=>"Без шаблона",
    'without_action'=>'Без action'
);
$LANG_JS_ARRAY['admin']=array(
    'confirm_delete'=>"Вы подтверждаете удаление?",
    'confirm_recover'=>"Вы подтверждаете восстановление?"
);
$LANG_ARRAY['admin']=array(
    'add'=>'Добавить',
    'delete'=>'Удалить',
    'update'=>'Обновить'
);
$LANG_ARRAY['admin_users']=array(
    'user_deleted_ok'=>'Пользователь успешно удален',
    'user_deleted_error'=>'Ошибка удаления пользователя',
    'create_user'=>'Создать пользователя',
    'confirm_email'=>'Подтвердить Email',
    'account_activated'=>'Активирован',
    'confirm_email_caption'=>'Пользователю будет необходимо кликнуть по ссылке в Email',
    'user_role'=>'Роль'
);