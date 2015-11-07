<?php  
include("../class.auth.php");   

$auth = new auth();

//~ user exit
if (isset($_REQUEST['exit'])) 
	$auth->exit_user();
	
if (isset($_REQUEST['login']))
	if(!$auth->authorization()) $error = $auth->error_reporting();

//~ Check auth
if (!$auth->check())  print $error;

?>