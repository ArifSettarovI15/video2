<?php

$LANG_ARRAY['general']=array(
    'save'=>'Сохранить'
);

$LANG_ARRAY['users']=
            array(
                // Registration
                'register'=>'Регистрация',
                'register_title'=>'Создание учетной записи',
                'register_sub_title'=>'Создайте аккаунт, чтобы получить дополнительные привелегии',
                'login'=>'Логин',
                'email'=>'Email',
                'enter_email'=>'Введите Email',
                'enter_login'=>'Введите логин',
                'enter_name'=>'Введите имя',
                'enter_lastname'=>'Введите фамилию',
                'enter_phone'=>'Введите телефон',
                'enter_city'=>'Введите населенный пункт',
                'enter_password'=>'Введите пароль',
                'enter_password2'=>'Введите пароль для проверки еще раз',
                'enter_correct_email'=>'Введите корректный Email',
                'password_different'=>'Введенные пароли не совпадают',
                'select_role'=>'Выберите роль',
                'password'=>'Пароль',
                'password_repeat'=>'Подтвердите пароль',
                'old_password'=>'Текущий пароль',
                'register_action'=>'Зарегистрироваться',
                'login_exist'=>'Такой логин уже существует. Выберите другой',
                'email_exist'=>'Такой Email уже существует',
                'user_create_error'=>'Ошибка создания аккаунта',
                'account_created'=>'Аккаунт успешно создан!',
                'account_confirm_email_title'=>'Активация аккаунта',
                'confirm_send'=>'На указанный Вами Email <b>^s1$</b> отправлено письмо с инструкциями по активации аккаунта.',
                'confirm_send_error'=>'Возникла ошибка при отправке письма на Ваш Email.',
                'email_send_error_desc'=>'Свяжитесь с администрацией сайта для устранения ошибки!',
                'enter_phone_number'=>'Пожалуйста, укажите номер в федеральном формате: код оператора и 7 цифр номера телефона',
                'user_name'=>'Имя',
                'user_lastname'=>'Фамилия',
                'user_phone'=>'Телефон',
                
                // Confirmation
                'confirm'=>'Активация аккаунта',
                'confirm_ok'=>'Ваш аккаунт успешно активирован. Теперь Вы можете зайти на сайт, используя свой логин и пароль.',
                'confirm_error'=>'Срок действия активации аккаунта по данной ссылке истек. Чтобы получить новую ссылку, необходимо заново ввести свой логин и пароль.',
                'confirm_wait'=>'Ваш аккаунт еще не активирован. Проверьте свой Email <b>^s1$</b>, чтобы активировать аккаунт.',
                'confirm_resend'=>'Ваш аккаунт еще не активирован. Вам отправлено новое письмо на email <b>^s1$</b> с инструкциями по активации аккаунта.',
                'confirm_change_email'=>'Чтобы изменить текущий Email Вашего аккаунта на другой, перейдите по ссылке <a href="^s1$">^s1$</a>',
                'confirm_email_text'=>'Для активации аккаунта, кликните по ссылке ниже или скопируйте и откройте ее в новом окне',
                
                // Login
                'login_form'=>'Авторизация',
                'login_button'=>'Войти',
                'strike_error'=>'Вы превысили количество неправильных попыток ввода логина и пароля. Попробуйте войти позже',
                'login_error'=>'Вы ввели неправильный логин или пароль',
                'login_error2'=>'Вы ввели неправильный Email или пароль',
                'login_ok'=>'Вы успешно авторизировались на сайте',

                // Forgot password
                'forgot_password'=>'Забыли пароль?',
                'forgot_action'=>'Восстановить',
                'forgot_form'=>'Восстановление пароля',
                'forgot_pare_error'=>'Пользователь с таким логином и Email не существует',
                'forgot_strike'=>'Вы превысили количество неправильных попыток восстановления пароля. Попробуйте позже',
                'account_forgot_email_title'=>'Восстановление пароля',
                'forgot_email_text'=>'Для восстановления аккаунта, кликните по ссылке ниже или скопируйте и откройте ее в новом окне',
                'forgot_send_error'=>'',
                'forgot_send'=>'Инструкция по восстановлению пароля отправлена на Ваш Email <b>^s1$</b>',
                
                // Logout
                'logout_ok'=>'Вы успешно вышли из своего аккаунта',
                
                // Recover
                'recover_error'=>'Срок действия восстановления пароля по данной ссылке истек.',
                'recover_set_password'=>'Установите новый пароль',
                'recover_error_desc'=>'Начните процесс восстановления пароля заново',
                'recover'=>'Восстановление пароля',
                'recover_go'=>'Установить',
                'recover_ok'=>'Пароль успешно изменен. Теперь Вы можете авторизоваться на сайте с новым паролем.',
                
                // Change Email
                'change_email'=>'Смена Email',
                'change_email_action'=>'Сменить Email' ,
                'change_email_ok'=>'Ваш Email успешно обновлен.',
                'change_email_error'=>'Срок действия изменения Email по данной ссылке истек. Попробуйте пройти процедуру еще раз.',
                'account_change_email_title'=>"Смена Email",
                'change_email_sent'=>'Инструкции по смене Email отправлены на <b>^s1$</b>.',
                
                // Roles
                'user_role'=>'Роль',
                'user_role_1'=>'Пользователь',
                'user_role_2'=>'Администратор',

                // Profile
                'user_profile'=>'Профиль',
                'account_updated'=>'Профиль успешно обновлен'
    );

$LANG_JS_ARRAY['users']=array(
        'fields_error'=>'Заполните указанные поля'
    );

$LANG_ARRAY['error']=array(
    'page_name'=>'Ошибка',
    'site_error'=>'Ошибка на сайте'
);
$LANG_ARRAY['error_codes']=array(
    'page_not_found'=>'Страница не найдена',
    'error_not_found'=>'Ошибка не найдена',
    'access_denied'=>'Доступ к странице запрещен',
    'account_already_activated'=>'Аккаунт уже активирован',
    'composer_error'=>'Установите модули Comsposer',
    'module_error'=>'Ошибка подключения модуля',
    'action_error'=>'Ошибка подключения action',
    'mysql_connect_error'=>'Ошибка соединения с базой данных',
    'database_error'=>'Ошибка базы данных',
    'user_not_exist'=>'Такого пользователя нет',
    'object_not_exist'=>'Объект не найден',
    'empty_result'=>'Данный раздел пуст'
);
include_once ('ru/ajax.php');
include_once ('ru_admin.php');