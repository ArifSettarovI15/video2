<?php
$Main->user->PagePrivacy('admin');
$Main->input->clean_array_gpc('r', array(
    'module'=>TYPE_STR,
    'input_name' => TYPE_STR,
    'folder_name' => TYPE_STR,
    'sub_folder'=>TYPE_STR,
    'multiple'=>TYPE_BOOL
));

$filter_options=array();
$filter_options['file_folder']=$Main->GPC['folder_name'];
if ($Main->GPC['sub_folder']!='') {
    $filter_options['file_folder'].='/'.$Main->GPC['sub_folder'];
}
$image_data=array(
    'input_name'=>$Main->GPC['input_name'],
    'module'=>$Main->GPC['module'],
    'folder'=>$Main->GPC['folder_name'],
    'sub_folder'=>$Main->GPC['sub_folder'],
    'multiple'=>true,
    'files'=>array(
        array()
    )
);

$variables=array(
    'image_data'=>$image_data,
    'multi'=>$Main->GPC['multiple']
);



$FilesPaging =new ClassPaging($Main);
$FilesPaging->data=$Main->files->GetFiles($filter_options,$FilesPaging->per_page,$FilesPaging->sql_start);

$FilesPaging->total=$Main->files->GetFilesTotal($filter_options);
$FilesPaging->Display('files/list_table.html.twig',$variables);