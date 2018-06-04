<?php
namespace App\Entity;

use \Core\Entity\Entity;

define('USER_ID', 0);
define('USER_LOGIN', 1);
define('USER_EMAIL', 2);
define('USER_MDPTOKEN', 3);

class Utilisateur extends Entity
{	
	private $sqlfields;

	// ##############################################################################
	// Constructeur de classe
	// Crée les variable de classe en fonction des champs de la table
	// ##############################################################################
	public function __construct($id, $type = USER_ID)
	{
		parent::__construct();
		
		$this->sqlfields = 'Id, NomDeCompte, Email, MotDePasse, Nom, Prenom, ValidationToken';
		
		switch($type)
		{
			case USER_ID:
			{
				$this->getById($id);
				break;
			}
			case USER_LOGIN:
			{
				$this->getByLogin($id);
				break;
			}
			case USER_EMAIL:
			{
				$this->getByEmail($id);
				break;
			}
			case USER_MDPTOKEN:
			{
				$this->getByMdpToken($id);
				break;
			}
		}			
	}
	
	// ##############################################################################
	// Récupération de données via le token mot de passe
	// ##############################################################################
	private function getByMdpToken(string $token)
	{		
		$result = $this->db->query('SELECT '.$this->sqlfields.' FROM Utilisateurs WHERE PasswordToken = ?', array($token));
		
		if(is_array($result) && count($result) > 0)
		{
			$this->PushInfo($result);
		}
	}
	
	// ##############################################################################
	// Récupération de données via l'email
	// ##############################################################################
	private function getByEmail(string $email)
	{		
		$result = $this->db->query('SELECT '.$this->sqlfields.' FROM Utilisateurs WHERE Email = ?', array($email));
		
		if(is_array($result) && count($result) > 0)
		{
			$this->PushInfo($result);
		}
	}
	
	// ##############################################################################
	// Récupération de données via l'id
	// ##############################################################################
	private function getById(int $id)
	{		
		$result = $this->db->query('SELECT '.$this->sqlfields.' FROM Utilisateurs WHERE Id = ?', array($id));
		
		if(is_array($result) && count($result) > 0)
		{
			$this->PushInfo($result);
		}
	}
	
	// ##############################################################################
	// Récupération de données via le nom de compte
	// ##############################################################################
	private function getByLogin(string $login)
	{
		$result = $this->db->query('SELECT '.$this->sqlfields.' FROM Utilisateurs WHERE NomDeCompte = ?', array($login));
		
		if(is_array($result) && count($result) > 0)
		{
			$this->PushInfo($result);
		}
	}
	
	// ##############################################################################
	// Fonction qui retourne si le compte ets actif ou non
	// ##############################################################################	
	public function IsActive() : bool
	{
		return ($this->ValidationToken == '') ? true : false;
	}
	
	// ##############################################################################
	// Fonction qui retourne l'email
	// ##############################################################################
	public function getEmail() : string
	{
		return $this->Email;
	}
	
	// ##############################################################################
	// Fonction qui retourne le nom complet
	// ##############################################################################
	public function getFullname() : string
	{
		return (isset($this->Nom) && isset($this->Prenom)) ? ucfirst($this->Nom).' '.ucfirst($this->Prenom) : '';
	}
	
	// ##############################################################################
	// Fonction de vérification ud mot de passe
	// ##############################################################################
	public function VerifyPassword($mdp)
	{
		
	}
	
	// ##############################################################################
	// Fonction d'edition du token de changement de mot de passe
	// ##############################################################################
	public function setPasswordToken() : string
	{
		$token = md5(uniqid(rand(), TRUE));
		
		$reponse = $this->db->Update('UPDATE Utilisateurs SET PasswordToken = :token WHERE Id = :id', array('id' => $this->Id, 'token' => $token));
		
		return $token;
	}
	
	// ##############################################################################
	// Fonction d'edition du mot de passe (sans ancien mdp)
	// ##############################################################################
	public function setForcePassword($newpassword)
	{
		$mdpsql = password_hash(PASSWORD_HASH_START.$this->NomDeCompte.':'.$newpassword.PASSWORD_HASH_END, PASSWORD_DEFAULT);
		$reponse = $this->db->Update('UPDATE Utilisateurs SET MotDePasse = :mdp, PasswordToken = "" WHERE Id = :id', array('id' => $this->Id, 'mdp' => $mdpsql));
	}
}
?>