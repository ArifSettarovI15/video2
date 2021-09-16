<?php
$filter_s=array();
$filter_s['key']='about';
$filter_options['show_order']=true;
$Main->template->global_vars['fields']['about']=$SettingsClass->GetGroupValues($filter_s);

