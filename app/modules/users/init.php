<?php
$Main->user->CheckUserActive($Main->user_info);
$Main->template->global_vars['user_info']=$Main->user->PrepareUserTemplate($Main->user_info);
$Main->user_profile=$Main->user->GetUserProfile($Main->user_info['user_id']);
$Main->template->global_vars['user_profile']=$Main->user_profile;

