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
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			return false;


		$headers['MIME-Version'] = '1.0';
		$headers['Content-type'] = 'text/html; charset=iso-8859-1';
	    $headers['To'] = 'Jordan <thejordan01@gmail.com>';
		$headers['From'] = $name.' <'.$email.'>';
		
		$corp		= 'Bonjour,
		<br /><br />
		Vous recevez un message en provenance de votre blog de <b>'.$name.'</b> ('.$email.')
		<br /><br />
		<b>Vous trouverez le message ci-dessous</b><br />'.$message;
		
		$result = mail('thejordan01@gmail.com', 'Blog - Contact', $corp, $headers);
		
		return $result;
	}
}
?>