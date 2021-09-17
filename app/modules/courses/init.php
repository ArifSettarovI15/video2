<?php


require_once 'models/courses.php';

$Courses = new Courses($Main);
$Main->courses = $Courses;
