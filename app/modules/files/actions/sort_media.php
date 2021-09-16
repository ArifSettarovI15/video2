<?php
$Main->user->PagePrivacy('admin');
$Main->input->clean_array_gpc('r', array(
    'data_sort' => TYPE_ARRAY_UINT,
    'item_id'=>TYPE_UINT,
    'name'=>TYPE_STR
));
$Main->files->UpdateFileIdsItemsSort($Main->GPC['name'],$Main->GPC['item_id'],$Main->GPC['data_sort']);

$array['status']=true;
$Main->template->DisplayJson($array);
