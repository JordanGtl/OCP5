<?php
namespace App\Entity;

use \Core\Entity\Entity;

define('USER_ID', 0);
define('USER_LOGIN', 1);
define('USER_EMAIL', 2);
define('USER_MDPTOKEN', 3);
define('USER_ACTIVATETOKEN', 4);

class Utilisateur extends Entity
{	
	private $sqlfields;

	// ##############################################################################
	// Constructeur de classe
	// Crée les variable de classe en fonction des champs de la table
	// ##############################################################################
	public function __construct($result)
	{
		parent::__construct();
		
		$this->Hydrate($result);
		
		//////////////////////////////////////////////////
		
		/*$this->sqlfields = 'Id, NomDeCompte, Email, MotDePasse, Nom, Prenom, ValidationToken';
		
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
			case USER_ACTIVATETOKEN:
			{
				$this->getByActivateToken($id);
				break;
			}
		}	*/		
	}
	
	// ##############################################################################
	// Fonction qui retourne si le compte ets actif ou non
	// ##############################################################################	
	public function IsActive() : bool
	{
		return (isset($this->ValidationToken) && $this->ValidationToken == '') ? true : false;
	}
	
	// ##############################################################################
	// Fonction qui active le compte
	// ##############################################################################	
	public function setActive()
	{
		$this->ValidationToken = '';
	}
	
	// ##############################################################################
	// Fonction qui retourne l'email
	// ##############################################################################
	public function getId() : int
	{
		return $this->Id ?? 0;
	}
	
	// ##############################################################################
	// Fonction qui retourne l'email
	// ##############################################################################
	public function getEmail() : string
	{
		return $this->Email ?? '';
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
	public function getPasswordHash()
	{
		return $this->MotDePasse ?? '';
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
	public function setPassword($newpassword)
	{
		if(isset($this->MotDePasse))
		{
			if(isset($this->NomDeCompte))
			{
				$this->MotDePasse = password_hash(PASSWORD_HASH_START.$this->NomDeCompte.':'.$newpassword.PASSWORD_HASH_END, PASSWORD_DEFAULT);
			}
			else
				throw new Exception('Aucun objet nom de compte n\'existe pour l\'entité "Utilisateur"');
		}
		else
			 throw new Exception('Aucun objet mot de passe n\'existe pour l\'entité "Utilisateur"');
	}
}
?>