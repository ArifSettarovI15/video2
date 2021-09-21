<?php


$Paging = new ClassPaging($Main, 25, false, false );

$Paging->Display('courses/manage/videos/videos_table.twig');