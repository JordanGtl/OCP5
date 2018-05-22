<?php
// Controler home
$home = new Home($db);

if(isset($_POST['email']))
{
	$parseur->vars['mailresult'] = ($home->SendMail()) ? '<span class="Succes">Email envoyé avec succès</span>' : '<span class="Fail">Une erreur est survenue lors de l\'envoi du mail</span>';
	$parseur->vars['token'] = $_SESSION['token'];
}
else
{
	# Protection CSRF
	$_SESSION['token'] = md5(uniqid(rand(), TRUE));
	
	$parseur->vars['mailresult'] = '';
	$parseur->vars['token'] = $_SESSION['token'];
}
?>