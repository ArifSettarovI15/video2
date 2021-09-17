<?php
$Main->user->PagePrivacy('admin');
$array = array();
if ($Main->GPC['action'] === 'delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_UINT
    ));

    $Courses->catalog->CreateModel();
    $Courses->catalog->model->columns_where->getId()->setValue($Main->GPC['object_id']);
    $status=$Courses->catalog->Delete();

    $array=array();
    $array['status']=$status;
    if ($status) {
        $array['text']='Объект успешно удален';
    }
    else {
        $array['text']='Ошибка удаления объекта';
    }
    $Main->template->DisplayJson($array);
}
if ($Main->GPC['action']=='sort_table') {
    $Main->input->clean_array_gpc('r', array(
        'data_sort' => TYPE_ARRAY_UINT
    ));
    $pos=0;
    $Courses->catalog->CreateModel();

    foreach ($Main->GPC['data_sort'] as $line_key) {
        $Courses->catalog->model->columns_where->getId()->setValue($line_key);
        $Courses->catalog->model->columns_update->getSort()->setValue($pos);
        $Courses->catalog->Update();
        $pos++;
    }

    $array['status']=true;
    $array['text']='Позиции обновлены';
    $Main->template->DisplayJson($array);
}
$Paging = new ClassPaging($Main, 15, false,false);
$Paging->show_per_page=false;

$Courses->catalog->CreateModel();
$Courses->catalog->model->setSelectField($Courses->catalog->model->getTableName().".*");
$Courses->catalog->model->SetJoinImage('icon', $Courses->catalog->model->GetTableItemNameSimple('icon'));
$Courses->catalog->model->setOrderBy('catalog_sort');
$Courses->catalog->model->setStart($Paging->sql_start);
$Courses->catalog->model->setCount($Paging->per_page);
$Paging->total = $Courses->catalog->GetTotal();
$Paging->data = $Courses->catalog->GetList();

$page_name='Направления';
$Main->template->SetPageAttributes(
    array(
        'title'=>$page_name,
        'keywords'=>'',
        'desc'=>''
    ),
    array(
        'breadcrumbs'=>array(
            array(
                'title'=>$page_name
            )
        ),
        'title'=>$page_name
    )
);

$Paging->Display('courses/manage/catalog/table.twig',$array);