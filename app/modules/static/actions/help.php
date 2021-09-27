<?php

$Main->user->PagePrivacy();

$breadcrumbs = array();
$breadcrumbs[] = array(
    'title'=>'Поддержка',
);
if ($Main->GPC['action'] == 'help_need'){
    $Main->input->clean_array_gpc('r', [
        'name'=>TYPE_STR,
        'email'=>TYPE_STR,
        'type'=>TYPE_STR,
        'comment'=>TYPE_STR,
    ]);
    $title   = 'Пользователь обратился в разделе Поддержка';
    $mail_to = $Main->template->global_vars['fields']['about']['email_notify'];

    $body = $Main->template->Render( 'static/email_write.twig',
        array(
            'name'=>$Main->GPC['name'],
            'email'=>$Main->GPC['email'],
            'type'=>$Main->GPC['type'],
            'comment'=>$Main->GPC['comment'],

        )
    );
    $aa = array( $Main->config['system']['email_addr'] => $Main->template->global_vars['fields']['about']['about_title'] );

    $message = ( new Swift_Message( $title ) )
        ->setFrom( $aa )
        ->setTo( [ $mail_to ] )
        ->setBody( $body, 'text/html' );

    try {
        $result = $Main->mailer->send( $message );
    } catch ( \Swift_TransportException $e ) {
        $response = $e->getMessage();
    }
    $Main->template->DisplayJson(['status'=>true, 'text'=>'Ваше обращение отправлено модератору']);
}

$page_name='Поддержка';
$Main->template->SetPageAttributes(
    array(
        'title' => $page_name,
        'meta'=>'about',
        'desc' => ''
    ),
    array(
        'breadcrumbs' => $breadcrumbs,
        'title' => $page_name,
        'back_url' => BASE_URL.'/',
        'background'=>BASE_URL.'/assets/images/static/blog_bg.jpg',

    ),
);

$filter_s=array();
$filter_s['key']='company';
$filter_options['show_order']=true;
$company=$SettingsClass->GetGroupValues($filter_s);


$Main->template->Display(compact('company'));