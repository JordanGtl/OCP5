<?php
// Controler inscription
if(isset($_POST['login']))
{
	$token = $_SESSION['token'];
}
else
{
	# Protection CSRF
	$token = md5(uniqid(rand(), TRUE));
	$_SESSION['token'] = $token;
}
?>