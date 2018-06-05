<?php
namespace App\Entity;

use \Core\Entity\Entity;

class Commentaire extends Entity
{	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct($result)
	{
		parent::__construct();
		
		$this->Hydrate($result);
	}
	
	// ##############################################################################
	// Fonction qui retourne le contenu
	// ##############################################################################
	public function getContenu() : string
	{
		return $this->Contenu;
	}
	
	// ##############################################################################
	// Fonction qui retourne la date formaté
	// ##############################################################################
	public function getFormatedDate() : string
	{
		$time = strtotime($this->Date);
		return date('d/m/Y', $time).' &agrave; '.date('H:i', $time);
	}
	
	// ##############################################################################
	// Fonction qui retourne l'auteur
	// ##############################################################################
	public function getAuteur() : string
	{
		return $this->Auteur ?? 'Inconnu'; 
	}
	
	// ##############################################################################
	// Fonction qui retourne l'id du post parent
	// ##############################################################################
	public function getParentPost() : string
	{
		return $this->IdPost;
	}
	
	// ##############################################################################
	// Fonction qui indique le statut (publié ou non-publié) du commentaire
	// ##############################################################################
	public function IsPublied() : bool
	{
		return boolval($this->Statut);
	}
}
?>