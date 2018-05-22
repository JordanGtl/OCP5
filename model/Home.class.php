<?php
class Home
{
	private $bdd;
	
	# Constructeur de classe
	public function __construct(PDO $bdd)
	{
		$this->bdd = $bdd;
	}
	
	# Envoie du mail de contact
	public function SendMail() : bool
	{
		$message 	= htmlentities(nl2br($_POST['message']));
		$name 		= htmlentities($_POST['name']);
		$email 		= htmlentities($_POST['email']);
		
		$corp		= 'Bonjour,
		<br /><br />
		Vous recevez un message en provenance de votre blog de <b>'.$name.'</b> ('.$email.')
		<br /><br /
		><b>Vous toruverez le message ci-dessous</b><br />'.$message;
		
		$result = mail('thejordan01@gmail.com', 'Blog - Contact', $corp);
		
		return $result;
	}
}
?>