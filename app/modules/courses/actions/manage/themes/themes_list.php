<?php
$Main->user->PagePrivacy('admin');
$array = array();
if ($Main->GPC['action'] === 'delete') {
    $Main->input->clean_array_gpc('r', array(
        'object_id' => TYPE_UINT
    ));

    $Courses->themes->CreateModel();
    $Courses->themes->model->columns_where->getId()->setValue($Main->GPC['object_id']);
    $status=$Courses->themes->Delete();

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
    $Courses->themes->CreateModel();

    foreach ($Main->GPC['data_sort'] as $line_key) {
        $Courses->themes->model->columns_where->getId()->setValue($line_key);
        $Courses->themes->model->columns_update->getSort()->setValue($pos);
        $Courses->themes->Update();
        $pos++;
    }

    $array['status']=true;
    $array['text']='Позиции обновлены';
    $Main->template->DisplayJson($array);
}
$Paging = new ClassPaging($Main, 15, false,false);
$Paging->show_per_page=false;

$Courses->themes->CreateModel();
$Courses->themes->model->setSelectField($Courses->themes->model->getTableName().".*");
$Courses->themes->model->SetJoinImage('icon', $Courses->themes->model->GetTableItemNameSimple('icon'));
$Courses->themes->model->setOrderBy('theme_sort');
$Courses->themes->model->setStart($Paging->sql_start);
$Courses->themes->model->setCount($Paging->per_page);
$Paging->total = $Courses->themes->GetTotal();
$Paging->data = $Courses->themes->GetList();

$page_name='Курсы';
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

$Paging->Display('courses/manage/themes/themes_table.twig',$array);