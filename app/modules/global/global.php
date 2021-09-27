<?php
$filter_s=array();
$filter_s['key']='about';
$filter_options['show_order']=true;
$Main->template->global_vars['fields']['about']=$SettingsClass->GetGroupValues($filter_s);

$filter_options=array();
$filter_options['content_type']='pages';
$filter_options['show_order']=true;

$Main->template->global_vars['pages']=$ContentClass->GetContentList($filter_options, 200, 0, 1);

$filer_s = array();
$filer_s['key'] = 'socials';
$filter_options['show_order']=true;
$Main->template->global_vars['socials']=$SettingsClass->GetGroupValues($filer_s)['socials_list'];

