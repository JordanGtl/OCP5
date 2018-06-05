<?php
namespace App\Model;

use \App\Entity\Commentaire;

class Comments
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
	public function getCommentListByPostId(int $id) : array
	{
		$comments = array();
		
		$req 	= $this->db->query('SELECT PostsCommentaire.Id, CONCAT(Utilisateurs.Nom , " " , Utilisateurs.Prenom) AS Auteur , PostsCommentaire.Date, PostsCommentaire.Contenu 
		FROM PostsCommentaire 
		INNER JOIN Utilisateurs ON Utilisateurs.Id = PostsCommentaire.Auteur 
		WHERE PostsCommentaire.Statut = 1 AND PostsCommentaire.IdPost = ?
		ORDER BY PostsCommentaire.Date DESC', array($id));
		
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
	public function Add()
	{
		$id 		= intval($_GET['id']);
		$auteur 	= intval($_SESSION['id']);
		$contenu 	= nl2br(htmlentities($_POST['com']));
		$date 		= date('Y-m-d H:i:s', time());
		
		$reponse = $this->db->Insert('INSERT INTO PostsCommentaire (IdPost, Auteur, Date, Contenu, Statut) VALUES (:idpost, :auteur, :date, :contenu, 0)',
		array('idpost' => $id, 'auteur' => $auteur, 'date' => $date, 'contenu' => $contenu));												
	}
}
?>