<?php
namespace App\Entity;


use \Core\Entity\Entity;

class Post extends Entity
{
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct($id)
	{
		parent::__construct();
		
		$result = $this->db->query('SELECT Titre, Contenu, Chapo, Picture, DerniereModification AS Date FROM BlogPosts');
		
		if(is_array($result) && count($result) > 0)
		{
			$this->PushInfo($result);
		}
	}
	
	// ##############################################################################
	// Fonction qui retourne le titre
	// ##############################################################################
	public function getTitle()
	{
		return $this->Titre;
	}
	
	// ##############################################################################
	// Fonction qui retourne le contenu
	// ##############################################################################
	public function getContenu()
	{
		return $this->Contenu;
	}
	
	// ##############################################################################
	// Fonction qui retourne le chapo
	// ##############################################################################
	public function getChapo()
	{
		return $this->Chapo;
	}
	
	// ##############################################################################
	// Fonction qui retourne l'image
	// ##############################################################################
	public function getPicture()
	{
		return $this->Picture;
	}
	
	// ##############################################################################
	// Fonction qui retourne la date formatée
	// ##############################################################################
	public function getFormatedDate()
	{
		return date('d/m/Y H:i', strtotime($this->Date));
	}
	
}
?>