<?php

if ($Main->GPC['action']=='delete_file'){
    $Main->input->clean_array_gpc('r', array(
        'order_uid'=>TYPE_STR,));
    $file_id = $FilesClass->GetFileIdByName($Main->GPC["order_uid"]);
    $result = $FilesClass->DeleteImage((int)$file_id["file_id"]);
    if ($result){
        $Main->template->DisplayJson(array('status'=>true, "text"=>"Фото удалено"));
    }
    else{
        $Main->template->DisplayJson(array('status'=>false, "text"=>"Что то пошло не так!"));
    }
}

if ($Main->GPC['action']=='upload_image') {
    $Main->input->clean_array_gpc('r', array(
        'input_name'=>TYPE_STR,
        'module'=>TYPE_STR,
        'folder'=>TYPE_STR,
        'place_id'=>TYPE_STR,
        'sub_folder'=>TYPE_STR,
        'filename'=>TYPE_STR,
        'multiple'=>TYPE_BOOL,
        'invert'=>TYPE_BOOL
    ));


    $upload_folder=$Main->GPC['folder'];
    if ($Main->GPC['sub_folder']!='') {
        $upload_folder.='/'.$Main->GPC['sub_folder'];
    }

    if ($upload_folder=='') {
        $upload_folder='content';
    }
    if ($Main->GPC['module']=='') {
        $Main->GPC['module']='content';
    }


    $Main->input->clean_array_gpc('f', array(
        $Main->GPC['input_name'] => TYPE_FILE
    ));
    $file_data=$Main->GPC[$Main->GPC['input_name']];

    $error='';

    $result_array=array();

    $FilesClass->current_module=$Main->GPC['module'];
    $FilesClass->upload_folder=$upload_folder;
	$file_uploaded_data = $FilesClass->UploadFile($file_data,$Main->GPC['filename'],'',array(
		'invert'=>$Main->GPC['invert']
	));



    if ($file_uploaded_data['status']) {
        $info = $FilesClass->GetFileInfo($file_uploaded_data['file_id']);
        $info['image_id'] = $info['file_id'];

        if ($Main->user_info['user_id']) {
	        $image_data=array(
		        'input_name'=> $Main->GPC['input_name'],
		        'multiple'=> $Main->GPC['multiple'],
	        );
	        $image_info=array(
		        'file_id'=>  $info['file_id'],
		        'icon_url'=> $info['icon_url']
	        );

	        $result_array['image']=$info['icon_url'];
	        $result_array['name']=basename($info['icon_url']);
	        $result_array['html'] = $Main->template->Render('files/upload_image_part.html.twig',
		        array(
			        'image_data' => $image_data,
			        'image_info'=>$image_info
		        )
	        );
        }
        else {
	        $result_array['image_id']=$info['file_id'];
	        $result_array['name']='Фото загружено';
        }

    }
    else {
        $result_array['error']=$file_uploaded_data['error'];
    }

    if ($Main->GPC['place_id']!='') {
        $result_array['place_id']=$Main->GPC['place_id'];
    }
    $Main->template->DisplayJson($result_array);
}
else {
    $Main->error->PageNotFound();
}
