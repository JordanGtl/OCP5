<?php
// Controler home
$home = new Home($db);

if(isset($_POST['email']))
{
	$mailresult = ($home->SendMail()) ? '<span class="Succes">Email envoyé avec succès</span>' : '<span class="Fail">Une erreur est survenue lors de l\'envoi du mail</span>';
}
else
{
	$mailresult = '';
}
?>