<?php
$Main->user->PagePrivacy('admin');
$Main->input->clean_array_gpc('r', array(
    'file_id'=>TYPE_UINT,
    'input_name'=>TYPE_STR,
    'multiple'=>TYPE_BOOL
));
$photo_array=array();
$info = $FilesClass->GetFileInfo($Main->GPC['file_id']);

if ($info) {
    $photo_array=array();
    $image_data=array(
        'input_name'=>$Main->GPC['input_name'],
        'multiple'=>$Main->GPC['multiple']
    );
    $image_info= array(
        'icon_url' => $info['icon_url'],
        'file_id' => $info['file_id']
    );

    $photo_array['html'] = $Main->template->Render('files/upload_image_part.html.twig',
        array(
           'image_data'=>$image_data,
            'image_info'=>$image_info
        )
    );
    $Main->template->DisplayJson($photo_array);
}
else {
    $Main->error->PageNotFound();
}