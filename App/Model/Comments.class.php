<?php
namespace App\Model;

use \App\Entity\Commentaire;

class Comments
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
	public static function getInstance() : Comment
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Posts();
		}
		
		return self::$_instance;
	}
	
	// ##############################################################################
	// Retourne la liste des commentaire via l'id du post
	// ##############################################################################
	public function getCommentListByPostId(int $ident) : array
	{
		$comments = array();
		
		$req 	= $this->database->query('SELECT PostsCommentaire.Id, CONCAT(Utilisateurs.Nom , " " , Utilisateurs.Prenom) AS Auteur , PostsCommentaire.Date, PostsCommentaire.Contenu 
		FROM PostsCommentaire 
		INNER JOIN Utilisateurs ON Utilisateurs.Id = PostsCommentaire.Auteur 
		WHERE PostsCommentaire.Statut = 1 AND PostsCommentaire.IdPost = ?
		ORDER BY PostsCommentaire.Date DESC', array($ident));
		
		foreach($req as $data)
		{
			$com = new Commentaire($data);
			$comments[] = $com;
		}
		
		return $comments;
	}
	
	// ##############################################################################
	// Fonction d'ajout d'un commentaire
	// ##############################################################################
	public function Add() : array
	{
		$ident 		= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
		$auteur 	= intval($_SESSION['id']);
		$contenu 	= nl2br(filter_input(INPUT_POST, 'com', FILTER_SANITIZE_STRING));
		$date 		= date('Y-m-d H:i:s', time());
		$token		= filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
		
		if($token == $_SESSION['token'])
		{
			$this->database->Insert('INSERT INTO PostsCommentaire (IdPost, Auteur, Date, Contenu, Statut) VALUES (:idpost, :auteur, :date, :contenu, 0)',
			array('idpost' => $ident, 'auteur' => $auteur, 'date' => $date, 'contenu' => $contenu));

			return array('statut' => 'Succes', 'message' => 'Votre message à été posté, il sera visible dès sa validation par l\'administration');
		}

		return array('statut' => 'Fail', 'message' => 'Le token de vérification est incorrect');
	}
}
?>