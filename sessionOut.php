<?php

ini_set('log_errors','on');
ini_set('error_log','php.log');

if(!empty($_SESSION)){
	error_log('セッションを削除します');
	session_destroy();
}

session_start();

error_log('最初のページへ遷移します。');
header("Location:select.php");
?>