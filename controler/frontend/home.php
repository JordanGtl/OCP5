<?php
// Controler home
$home = new Home($db);

if(isset($_POST['email']))
{
	$home->SendMail();
}
?>