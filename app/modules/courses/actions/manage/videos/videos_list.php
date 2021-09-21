<?php

$theme = $Courses->themes->GetItemById($Main->GPC['theme']);

$Paging = new ClassPaging($Main, 25, false, false );

$Courses->videos->CreateModel();
$Courses->videos->model->setSelectField($Courses->videos->model->getTableName().'.*, courses_blocks.*');
$Courses->videos->model->setOrderBy('video_sort');
$Courses->videos->model->columns_where->getTheme()->setValue($theme['theme_id']);
$Courses->videos->model->SetJoinImage('icon', $Courses->videos->model->GetTableItemName('icon'));
$Courses->videos->model->setJoin('LEFT JOIN courses_blocks ON courses_blocks.block_id = courses_videos.video_block');
$Paging->data = $Courses->videos->GetList();
$Paging->total = $Courses->videos->GetTotal();


$variables['theme'] = $theme;

$Paging->Display('courses/manage/videos/videos_table.twig',$variables);