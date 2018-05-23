<?php
$parseur->vars['register_message'] 	= '';
$parseur->vars['token'] 				= '';


// Controler inscription
if(isset($_POST['login']))
{
	# Inscription
	$membre 		= new Membres($db);
	$inscription 	= $membre->AjoutMembre();
	
	# Parseur
	$parseur->vars['register_message'] = $inscription['message'];	
	$parseur->vars['register_statut'] = ($inscription['statut'] == 0) ? 'Fail' : 'Succes';	
}
else
{
	# Protection CSRF
	$_SESSION['token'] = md5(uniqid(rand(), TRUE));
	
	# Parseur
	$parseur->vars['register_statut'] = 'None';	
}

$parseur->vars['token'] = $_SESSION['token'];
?>