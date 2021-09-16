<?php
$Main->user->PagePrivacy('admin');
$Main->input->clean_array_gpc('r', array(
    'file_id'=>TYPE_UINT
));
$info = $FilesClass->DeleteImage($Main->GPC['file_id']);
if ($info) {
    $array=array();
    $array['status']=true;
    $array['text']='Файл успешно удален';
    $array['html']='';
    $Main->template->DisplayJson($array);
}
else {
    $Main->error->ObjectNotFound();
}