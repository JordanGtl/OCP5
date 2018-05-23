<?php
class Membres
{
	private $bdd;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct(PDO $bdd)
	{
		$this->bdd = $bdd;
	}
	
	// ##############################################################################
	// Fonction d'inscription d'un membre
	// ##############################################################################
	public function AjoutMembre() : array
	{
		$login 		= strtolower(htmlentities($_POST['login']));
		$token 		= htmlentities($_POST['token']);
		$mdp 		= htmlentities($_POST['mdp']);
		$mdpr 		= htmlentities($_POST['mdpr']);
		$email 		= strtolower(htmlentities($_POST['email']));
		$nom 		= htmlentities($_POST['nom']);
		$prenom 	= htmlentities($_POST['prenom']);
		$captcha 	= htmlentities($_POST['g-recaptcha-response']);

		if(!empty($captcha))
		{
			if($this->CheckRecaptchaInfo($captcha))
			{
				if($token == $_SESSION['token'])
				{
					if(!empty($login))
					{
						if(!empty($mdp) || !empty($mdpr))
						{
							if($mdp == $mdpr)
							{
								if(!empty($email))
								{
									if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
									{
										if(!empty($nom))
										{
											if(!empty($prenom))
											{
												$reponse = $this->bdd->prepare('SELECT Id FROM Utilisateurs WHERE NomDeCompte = ?');
												$reponse->execute(array($login));
												$user = $reponse->fetch(PDO::FETCH_ASSOC);
												
												if(!isset($user['Id']))
												{
													$reponse = $this->bdd->prepare('SELECT Id FROM Utilisateurs WHERE Email = ?');
													$reponse->execute(array($email));
													$email = $reponse->fetch(PDO::FETCH_ASSOC);
													
													if(!isset($email['Id']))
													{
														$validationtoken = md5(uniqid(rand(), TRUE));
														$mdpsql = sha1('d5z8f5'.$login.':'.$mdp.'df57hf5');
														
														$reponse = $this->bdd->prepare('INSERT INTO Utilisateurs (NomDeCompte, MotDePasse, Nom, Prenom, Email, ValidationToken, Rang) VALUES (:login, :mdp, :nom, :prenom, :email, :validationtoken, 0)');
														$reponse->execute(array('login' => $login, 'mdp' => $mdpsql, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'validationtoken' => $validationtoken));
														
														// Ajouter ici l'envoie du mail de validation
														
														return array('statut' => 1, 'message' => '');
													}
													else
														return array('statut' => 0, 'message' => 'L\'email existe déjà dans la base de données');
												}
												else
													return array('statut' => 0, 'message' => 'Le nom de compte existe déjà');
											}
											else
												return array('statut' => 0, 'message' => 'Aucun prénom n\'est renseigné');
										}
										else
											return array('statut' => 0, 'message' => 'Aucun nom de famille n\'est renseigné');
									}
									else
										return array('statut' => 0, 'message' => 'L\'email saisie est incorrect');
								}
								else
									return array('statut' => 0, 'message' => 'Aucun email n\'a été renseigné');
							}
							else
								return array('statut' => 0, 'message' => 'Les deux mot de passe saisie ne sont pas identique');
						}
						else
							return array('statut' => 0, 'message' => 'Les deux champs mot de passe ne sont pas renseignés');
					}
					else
						return array('statut' => 0, 'message' => 'Aucun nom de compte renseigné');
				}
				else
					return array('statut' => 0, 'message' => 'Le token de vérification est incorrect');
			}
			else
				return array('statut' => 0, 'message' => 'Le captcha n\'est pas valide');
		}
		else
			return array('statut' => 0, 'message' => 'Aucun captcha valide renseigné');
	}
	
	// ##############################################################################
	// Fonction d'identification d'un membre
	// ##############################################################################
	public function AuthMembre()
	{
		
	}
	
	// ##############################################################################
	// Fonction de vérfication du captcha chez google
	// ##############################################################################
	private function CheckRecaptchaInfo(string $captcha) : bool
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=6Ldph1oUAAAAANWTm5ZBkT7mCf6HSXJYqg1-i4Ul&response=".$captcha);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = json_decode(curl_exec ($ch));
		curl_close ($ch);
		
		return boolval($output->success);
	}
	
	// ##############################################################################
	// Vérifie si le membre est connecté
	// ##############################################################################
	public function IsLogged() : bool
	{
		return (isset($_SESSION['login'])) ? true : false;
	}
}
?>