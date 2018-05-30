<?php
namespace App\Model;
use \Mailjet\Resources;

class Membres
{
	private static $_instance;
	private $db;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		$this->db = Database::getInstance();
	}
		
	// ##############################################################################
	// CRetourne l'instance de la classe
	// ##############################################################################
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Membres();
		}
		
		return self::$_instance;
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
												$usersql = $this->db->query('SELECT Id FROM Utilisateurs WHERE NomDeCompte = ?', array($login));

												if(!isset($usersql[0]->Id))
												{
													$emailsql = $this->db->query('SELECT Id FROM Utilisateurs WHERE Email = ?', array($email));

													if(!isset($emailsql[0]->Id))
													{
														$validationtoken = md5(uniqid(rand(), TRUE));
														$mdpsql = password_hash('d5z8f5'.$login.':'.$mdp.'df57hf5', PASSWORD_DEFAULT);
														
														$reponse = $this->db->Insert('INSERT INTO Utilisateurs (NomDeCompte, MotDePasse, Nom, Prenom, Email, ValidationToken, Rang) VALUES (:login, :mdp, :nom, :prenom, :email, :validationtoken, 0)',
														array('login' => $login, 'mdp' => $mdpsql, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'validationtoken' => $validationtoken));
														
														$this->SendRegisterMail($email, $prenom.' '.$nom, 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].'/index.php?page=6&token='.$token);
														
														return array('register_statut' => 'Succes', 'register_message' => 'Inscription réussie, activer votre compte via l\'email que nous vous avons envoyé.');
													}
													else
														return array('register_statut' => 'Fail', 'register_message' => 'L\'email existe déjà dans la base de données');
												}
												else
													return array('register_statut' => 'Fail', 'register_message' => 'Le nom de compte existe déjà');
											}
											else
												return array('register_statut' => 'Fail', 'register_message' => 'Aucun prénom n\'est renseigné');
										}
										else
											return array('register_statut' => 'Fail', 'register_message' => 'Aucun nom de famille n\'est renseigné');
									}
									else
										return array('register_statut' => 'Fail', 'register_message' => 'L\'email saisie est incorrect');
								}
								else
									return array('register_statut' => 'Fail', 'register_message' => 'Aucun email n\'a été renseigné');
							}
							else
								return array('register_statut' => 'Fail', 'register_message' => 'Les deux mots de passe saisie ne sont pas identiques');
						}
						else
							return array('register_statut' => 'Fail', 'register_message' => 'Les deux champs mot de passe ne sont pas renseignés');
					}
					else
						return array('register_statut' => 'Fail', 'register_message' => 'Aucun nom de compte renseigné');
				}
				else
					return array('register_statut' => 'Fail', 'register_message' => 'Le token de vérification est incorrect');
			}
			else
				return array('register_statut' => 'Fail', 'register_message' => 'Le captcha n\'est pas valide');
		}
		else
			return array('register_statut' => 'Fail', 'register_message' => 'Aucun captcha valide renseigné');
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
	// Fonction d'authentification d'un utilisateur
	// ##############################################################################
	public function AuthUser() : array
	{
		$login 	= strtolower(htmlentities($_POST['login']));
		$mdp 	= htmlentities($_POST['mdp']);
		$token 	= htmlentities($_POST['token']);

		$user = $this->db->query('SELECT Id, MotDePasse, ValidationToken FROM Utilisateurs WHERE NomDeCompte = ?', array($login));
		
		if($token == $_SESSION['token'])
		{
			if(isset($user[0]) && password_verify('d5z8f5'.$login.':'.$mdp.'df57hf5', $user[0]->MotDePasse))
			{
				if($user[0]->ValidationToken == '')
				{
					$_SESSION['login'] = $login;
					return array('register_statut' => 'Success', 'register_message' => 'Connexion réussie, vous allez être redirigé');
				}
				else
					return array('register_statut' => 'Fail', 'register_message' => 'Ce compte n\'est pas encore activé, consulter votre boite email.');
			}
			else
				return array('register_statut' => 'Fail', 'register_message' => 'Les identifiants saisie sont incorrect');	
		}
		else
			return array('register_statut' => 'Fail', 'register_message' => 'Le token de vérification est expiré');	
	}
	
	// ##############################################################################
	// Vérifie si le membre est connecté
	// ##############################################################################
	public static function IsLogged() : bool
	{
		return (isset($_SESSION['login'])) ? true : false;
	}
	
	public function SendRegisterMail($email, $name, $link)
	{
		$api = new \Mailjet\Client(MAILJET_ID, MAILJET_SECRET, true,['version' => 'v3.1']);
		$body = [
			'Messages' => [
				[
					'From' => [
						'Email' => "noreply@gtl-studio.com",
						'Name' => "GTL Studio"
					],
					'To' => [
						[
							'Email' => $email,
							'Name' => $name
						]
					],
					'TemplateID' => 405363,
					'TemplateLanguage' => true,
					
            'Variables' => [
                'nomcompte' => $name,
                'link' => $link
            ],
					"TemplateErrorDeliver" => true,
					"TemplateErrorReporting" => [
					"Email" => "thejordan01@gmail.com",
					"Name" => "Air traffic control"
					]
				]
			]
			
			
		];
		$response = $api->post(['send', ''/*, 'v3.1'*/], ['body' => $body]);
		$response->success();
	}
}
?>