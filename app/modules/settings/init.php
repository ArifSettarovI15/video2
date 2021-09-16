<?php
require_once 'models/settings.php';
$SettingsClass= new SettingsClass($Main);
$Main->settings=$SettingsClass;
