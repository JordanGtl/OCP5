<?php
namespace App\Entity;

use \Core\Entity\Entity;

class Utilisateur extends Entity
{	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct($id)
	{
		parent::__construct();
		
		if(strpos($id, '@') > 0)
			$this->getByEmail($id);
		if(is_numeric($id))
			$this->getById($id);
		else
			$this->getByLogin($id);
	}
	
	// ##############################################################################
	// Récupération de données via l'email
	// ##############################################################################
	private function getByEmail(string $email)
	{		
		$result = $this->db->query('SELECT Id, NomDeCompte, Email, MotDePasse, Nom, Prenom, ValidationToken FROM Utilisateurs WHERE Email = ?', array($email));
		
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
		$result = $this->db->query('SELECT Id, NomDeCompte, Email, MotDePasse, Nom, Prenom, ValidationToken FROM Utilisateurs WHERE Id = ?', array($id));
		
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
		$result = $this->db->query('SELECT Id, NomDeCompte, Email, MotDePasse, Nom, Prenom, ValidationToken FROM Utilisateurs WHERE NomDeCompte = ?', array($login));
		
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
		return ucfirst($this->Nom).' '.ucfirst($this->Prenom);
	}
	
	// ##############################################################################
	// Fonction de vérification ud mot de passe
	// ##############################################################################
	public function VerifyPassword($mdp)
	{
		
	}
}
?>