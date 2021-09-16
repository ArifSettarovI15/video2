<?php
$Main->user->PagePrivacy('admin');

$edit=0;
$data_info=array();
if($Main->GPC['do']=='edit' OR $Main->GPC['action']=='process_edit'){
    $edit=1;
    $data_info=$SeoClass->GetSeoById($Main->GPC['id'],1);
    if ($data_info) {

    }
    else {
        $Main->error->PageNotFound();
    }
}


if ($Main->GPC['action']=='process_add' OR $Main->GPC['action']=='process_edit') {


    $Main->input->clean_array_gpc('r', array(
        'seo_title' => TYPE_STR,
        'seo_page_title' => TYPE_STR,
        'seo_url' => TYPE_STR,
        'seo_icon_text'=>TYPE_STR,
        'seo_keywords' => TYPE_STR,
        'seo_desc' => TYPE_STR,
        'text'=>TYPE_STR,
        'seo_id' => TYPE_UINT,
        'seo_skip_last' => TYPE_BOOL
    ));


    $data=$SeoClass->GetSeoByUrl($Main->GPC['seo_url']);
    $error='';
    if ($data and $data['seo_id']!=$Main->GPC['seo_id']) {
        $error='Seo с таким адресом уже существует';
    }
    elseif ($Main->GPC['seo_url']=='') {
        $error='Введите URL';
        $array['error_field'] = 'seo_url';
    }

    if ($error!='') {
        $array['status'] = false;
        $array['text']=$error;
    }
    else {
        $array['status'] = true;

        $text_id=$Main->text->SaveText($data_info['seo_text_id'], $Main->GPC['text']);

        if ($Main->GPC['action']=='process_edit') {
            $SeoClass->UpdateSeo($Main->GPC['seo_id'],$Main->GPC['seo_url'],$Main->GPC['seo_title'],$Main->GPC['seo_page_title'],
	            $Main->GPC['seo_keywords'],$Main->GPC['seo_desc'],$Main->GPC['seo_skip_last'],$Main->GPC['seo_icon_text'],$text_id);
            $array['text'] = 'Seo успешно обновлено';
            $id=$Main->GPC['content_id'];

        }
        else {
            $id=$SeoClass->AddSeo(
	            $Main->GPC['seo_url'],$Main->GPC['seo_title'],$Main->GPC['seo_page_title'],
	            $Main->GPC['seo_keywords'],$Main->GPC['seo_desc'],$Main->GPC['seo_skip_last'],$Main->GPC['seo_icon_text'],$text_id
            );
            $array['text'] = 'Seo успешно добавлено';
        }
    }
    $Main->template->DisplayJson($array);
}


if ($edit==1) {
    $a_name='Редактировать';
}
else {
    $a_name='Добавить';
}


$breadcrumbs=array();
$breadcrumbs[]=array(
    'title'=>'Seo',
    'link'=>BASE_URL.'/manager/seo/'
);

$page_name='Seo';


$breadcrumbs[]=array(
    'title'=>$a_name
);
$page_name=$a_name.' seo';
$Main->template->SetPageAttributes(
    array(
        'title'=>$page_name,
        'keywords'=>'',
        'desc'=>''
    ),
    array(
        'breadcrumbs'=>$breadcrumbs,
        'title'=>$page_name
    )
);


$Main->template->Display(array(
        'info'=>$data_info,
        'edit'=>$edit,
    )
);
