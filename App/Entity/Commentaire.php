<?php
namespace App\Entity;

use \Core\Entity\Entity;

class Commentaire extends Entity
{	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct($id)
	{
		parent::__construct();
		
		$result = $this->db->query('SELECT PostsCommentaire.Id, PostsCommentaire.IdPost, Utilisateurs.Nom, Utilisateurs.Prenom, PostsCommentaire.Date, PostsCommentaire.Contenu, PostsCommentaire.Statut 
		FROM PostsCommentaire 
		INNER JOIN Utilisateurs ON Utilisateurs.Id = PostsCommentaire.Auteur
		WHERE PostsCommentaire.Id = ?', array($id));
		
		if(is_array($result) && count($result) > 0)
		{
			$this->PushInfo($result);
		}
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
		return date('d/m/Y H:i', strtotime($this->Date));
	}
	
	// ##############################################################################
	// Fonction qui retourne l'auteur
	// ##############################################################################
	public function getAuteur() : string
	{
		return $this->Nom .' '. $this->Prenom; 
	}
	
	// ##############################################################################
	// Fonction qui retourne l'id du post parent
	// ##############################################################################
	public function getParentPost() : string
	{
		return this->IdPost;
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