<?php
namespace App\Model;

use \Mailjet\Resources;
use \App\Entity\Utilisateur;
use \Core\Objects\Session;

class Membres
{
	private static $_instance;
	private $database;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		$this->database = Database::getInstance();
	}
		
	// ##############################################################################
	// Retourne l'instance de la classe
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
		$login 		= strtolower(filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING));
		$token 		= filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
		$mdp 		= filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
		$mdpr 		= filter_input(INPUT_POST, 'mdpr', FILTER_SANITIZE_STRING);
		$email 		= strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
		$nom 		= filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
		$prenom 	= filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
		$captcha 	= filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING);

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
												$user = $this->database->query('SELECT Id FROM Utilisateurs WHERE NomDeCompte = ?', array($login));
												$user = new Utilisateur($user);
												
												if(!$user->Exist())
												{
													$usermail = $this->database->query('SELECT Id FROM Utilisateurs WHERE Email = ?', array($email));
													$usermail = new Utilisateur($usermail);

													if(!$usermail->Exist())
													{
														$validationtoken = md5(uniqid(rand(), TRUE));
														$mdpsql = password_hash(PASSWORD_HASH_START.$login.':'.$mdp.PASSWORD_HASH_END, PASSWORD_DEFAULT);
														
														$this->database->Insert('INSERT INTO Utilisateurs (NomDeCompte, MotDePasse, Nom, Prenom, Email, ValidationToken, Rang) VALUES (:login, :mdp, :nom, :prenom, :email, :validationtoken, 0)',
														array('login' => $login, 'mdp' => $mdpsql, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'validationtoken' => $validationtoken));
														
														$this->SendRegisterMail($email, $prenom.' '.$nom, 'http://'.$_SERVER['HTTP_HOST'].'/index.php?page=6&token='.$validationtoken);
														
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
		$request = curl_init();

		curl_setopt($request, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($request, CURLOPT_POST, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, "secret=6Ldph1oUAAAAANWTm5ZBkT7mCf6HSXJYqg1-i4Ul&response=".$captcha);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$output = json_decode(curl_exec ($request));
		curl_close ($request);
		
		return boolval($output->success);
	}
	
	// ##############################################################################
	// Fonction d'authentification d'un utilisateur
	// ##############################################################################
	public function AuthUser() : array
	{
		$login 	= strtolower(filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING));
		$mdp 	= filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
		$token 	= filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

		$user = $this->database->query('SELECT Id, MotDePasse, ValidationToken, Nom, Prenom FROM Utilisateurs WHERE NomDeCompte = ?', array($login));
		
		if($token == $_SESSION['token'])
		{
			if(isset($user[0]) && password_verify(PASSWORD_HASH_START.$login.':'.$mdp.PASSWORD_HASH_END, $user[0]->MotDePasse))
			{
				if($user[0]->ValidationToken == '')
				{
                    $session = new Session();
                    $session->vars['login'] = $login;
                    $session->vars['id'] = $user[0]->Id;
                    $session->vars['nom'] = $user[0]->Nom;
                    $session->vars['prenom'] = $user[0]->Prenom;

					header('location:index.php');
					return array('register_statut' => 'Succes', 'register_message' => 'Connexion réussie, vous allez être redirigé');
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
	// Fonction qui verifie l'association d'un token mot de passe à un compte
	// ##############################################################################
	public function VerifPasswordToken(string $token) : Utilisateur
	{
		$sql = $this->database->query('SELECT Id, Nom, Prenom FROM Utilisateurs WHERE PasswordToken = ?', array($token));
		
		$user = new Utilisateur($sql);
		
		return $user;
	}
	
	// ##############################################################################
	// Fonction mot de passe perdu
	// ##############################################################################
	public function PasswordLost() : array
	{
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
		
		if($token == $_SESSION['token'])
		{
			if(!empty($email)) {
                $sql = $this->database->query('SELECT Id, Email FROM Utilisateurs WHERE Email = ?', array($email));
                $user = new Utilisateur($sql);

                if ($user->Exist()) {
                    $token = $user->setPasswordToken();
                    $this->database->Update('UPDATE Utilisateurs SET PasswordToken = :token WHERE Id = :id', array('id' => $user->getId(), 'token' => $token));
                    $this->SendLostPasswordMail($user->getEmail(), $user->getFullname(), $_SERVER['HTTP_HOST'] . '/index.php?page=8&token=' . $token);

                    return array('statut' => 'Success', 'message' => 'L\'email de récupération à été envoyé');
                }

                return array('statut' => 'Fail', 'message' => 'L\'email n\'est associé à aucun compte');
            }

            return array('statut' => 'Fail', 'message' => 'Aucun email renseigné');
		}
		return array('statut' => 'Fail', 'message' => 'Le token de vérification est incorrect');
	}
	
	// ##############################################################################
	// Vérifie si le membre est connecté
	// ##############################################################################
	public static function IsLogged() : bool
	{
		return (isset($_SESSION['login'])) ? true : false;
	}
	
	// ##############################################################################
	// Fonction d'envoie du mail du mot de passe oublié
	// ##############################################################################
	public function SendLostPasswordMail($email, $name, $link)
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
					'TemplateID' => 410322,
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
	
	// ##############################################################################
	// Fonction d'envoie du mail d'inscription
	// ##############################################################################
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

	// ##############################################################################
	// Fonction d'envoie du mail d'inscription
	// ##############################################################################
	public function SendContactMail($email, $name, $message)
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
							'Email' => CONTACT_EMAIL,
							'Name' => CONTACT_NAME
						]
					],
					'TemplateID' => 455238,
					'TemplateLanguage' => true,

            'Variables' => [
                'nom' => $name,
                'message' => $message,
                'email' => $email
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
	
	// ##############################################################################
	// Fonction de récupération du mot de passe perdu
	// ##############################################################################
	public function SetLostPassword()
	{
		$token 	= filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
		$mdp 	= filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
		$mdpr 	= filter_input(INPUT_POST, 'mdpr', FILTER_SANITIZE_STRING);
		
		$sql 	= $this->database->query('SELECT Id, NomDeCompte, MotDePasse FROM Utilisateurs WHERE PasswordToken = ?', array($token));
		$user 	= new Utilisateur($sql);
		
		if($user->Exist())
		{
			if($mdp == $mdpr)
			{
				$user->setPassword($mdp);
				$this->database->Update('UPDATE Utilisateurs SET MotDePasse = :mdp, PasswordToken = "" WHERE Id = :id', array('id' => $user->getId(), 'mdp' => $user->getPasswordHash()));
				return array('Statut' => 'Succes', 'Message' => 'Mot de passe modifié avec succès, vous pouvez vous connecter.');
			}
			else
				return array('Statut' => 'Fail', 'Message' => 'Les deux mot de passes saisie ne sont pas identique.');
		}
		else
		{
			return array('Statut' => 'Fail', 'Message' => 'Aucun utilisateur associé à ce token.');
		}		
		
	}
	
	// ##############################################################################
	// Fonction d'activation d'un compte
	// ##############################################################################
	public function ActiveAccount() : array
	{
		$sql 	= $this->database->query('SELECT Id, NomDeCompte FROM Utilisateurs WHERE ValidationToken = ?', array(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING)));
		$user = new Utilisateur($sql);
		
		if($user->Exist())
		{
			$user->setActive();
			$this->database->Update('UPDATE Utilisateurs SET ValidationToken = "" WHERE Id = :id', array('id' => $user->getId()));
			return array('statut' => 'Succes', 'message' => 'L\'activation du compte est terminé, vous pouvez vous connecter dès maintenant');
		}
		else
		{
			return array('statut' => 'Fail', 'message' => 'Le token renseigné n\'est associé à aucun compte.');
		}		
	}

	public function getMyAccountPage()
    {
        $session = new Session();

        $sql 	= $this->database->query('SELECT Id, NomDeCompte, Nom, Prenom, Email, NomDeCompte FROM Utilisateurs WHERE Id = ?', array($session->vars['id']));
        $user = new Utilisateur($sql);

        return array('user' => $session->vars['login'], 'nom' => $user->getName(), 'prenom' => $user->getFirstname(), 'email' => $user->getEmail());
    }

    public function setMyAccountPage()
    {
        $session = new Session();

        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
        $mdpr = filter_input(INPUT_POST, 'mdpr', FILTER_SANITIZE_STRING);

        if($mdp == '' || $mdpr == '')
            return array('statut' => 'fail', 'message' => 'les deux champs doivent être renseignés pour modifier le mot de passe');

        if($mdp != $mdpr)
            return array('statut' => 'fail', 'message' => 'Les deux mots de passe saisie ne sont pas identiques');

        $sql 	= $this->database->query('SELECT Id, NomDeCompte, Nom, Prenom, Email, NomDeCompte, MotDePasse FROM Utilisateurs WHERE Id = ?', array($session->vars['id']));
        $user = new Utilisateur($sql);

        $user->setPassword($mdp);
        $this->database->Update('UPDATE Utilisateurs SET MotDePasse = :mdp, PasswordToken = "" WHERE Id = :id', array('id' => $user->getId(), 'mdp' => $user->getPasswordHash()));

        return array('statut' => 'Succes', 'message' => 'Le mot de passe a été modifié avec succès');
    }
}
?>