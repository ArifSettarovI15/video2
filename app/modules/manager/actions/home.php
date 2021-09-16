<?php
if($Main->GPC['do'] == 'manager'){
    $Main->error->PageNotFound();
}
$Main->user->PagePrivacy('admin');


$page_name='Панель управления';
$Main->template->SetPageAttributes(
    array(
        'title'=>$page_name,
        'keywords'=>'',
        'desc'=>''
    ),
    array(
        'breadcrumbs'=>array(

        ),
        'title'=>$page_name
    )
);

$Main->template->Display();

