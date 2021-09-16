<?php
require_once 'models/seo.php';
$SeoClass= new SeoClass($Main);
$Main->seo=$SeoClass;