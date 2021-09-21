<?php
$theme = $Courses->themes->GetItemById($Main->GPC['theme']);

$Paging = new ClassPaging($Main, 25, false, false );

$Courses->blocks->CreateModel();
$Courses->blocks->model->setOrderBy('block_sort');
$Paging->data = $Courses->blocks->GetList();
$Paging->total = $Courses->blocks->GetTotal();
$variables['theme'] = $theme;
$Paging->Display('courses/manage/blocks/blocks_table.twig',$variables);