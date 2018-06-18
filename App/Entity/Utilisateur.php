<?php
namespace App\Entity;

use \Core\Entity\Entity;
use Exception;

define('USER_ID', 0);
define('USER_LOGIN', 1);
define('USER_EMAIL', 2);
define('USER_MDPTOKEN', 3);
define('USER_ACTIVATETOKEN', 4);

class Utilisateur extends Entity
{	
	// ##############################################################################
	// Constructeur de classe
	// Crée les variable de classe en fonction des champs de la table
	// ##############################################################################
	public function __construct($result)
	{
		parent::__construct();
		
		$this->Hydrate($result);	
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
	// Fonction qui retourne le nom de famille
	// ##############################################################################
	public function getName() : string
	{
		return $this->Nom ?? '';
	}

	// ##############################################################################
	// Fonction qui retourne le prenom
	// ##############################################################################
	public function getFirstname() : string
	{
		return $this->Prenom ?? '';
	}

	// ##############################################################################
	// Fonction de vérification du mot de passe
	// ##############################################################################
	public function getPasswordHash()
	{
		return $this->MotDePasse ?? '';
	}
	
	// ##############################################################################
	// Fonction de récupération du rang de l'utilisateur
	// ##############################################################################
	public function getRank()
	{
		return $this->Rang ?? 0;
	}
	
	// ##############################################################################
	// Fonction d'edition du token de changement de mot de passe
	// ##############################################################################
	public function setPasswordToken() : string
	{
		$token = md5(uniqid(rand(), TRUE));

		if(isset($this->Token))
		    $this->Token = $token;

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