<?php
$parseur->vars['retour_inscription'] = '';
$parseur->vars['token'] = '';

// Controler inscription
if(isset($_POST['login']))
{
	$membre = new Membres($db);
	$parseur->vars['retour_inscription'] = $membre->AjoutMembre()['message'];	
}
else
{
	# Protection CSRF
	$_SESSION['token'] = md5(uniqid(rand(), TRUE));
}

$parseur->vars['token'] = $_SESSION['token'];
?>