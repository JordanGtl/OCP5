<?php
namespace App\Model;


use App\Entity\Post;

class Posts
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
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Posts();
		}
		
		return self::$_instance;
	}
	
	// ##############################################################################
	// Récupère les dernier posts
	// ##############################################################################
	public function getLastPost() : array
	{
		$req 	= $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Picture, BlogPosts.Chapo, BlogPosts.Auteur, 
		(SELECT COUNT(PostsCommentaire.Id) FROM PostsCommentaire WHERE PostsCommentaire.IdPost = BlogPosts.Id) AS NbrComment 
		FROM BlogPosts  
		LIMIT 0,6');
		$return = array();
		
		foreach($req as $data)
		{		
			$post = new Post((array)$data);
			$return[] = $post;
		}
		
		return $return;
	}
	
	// ##############################################################################
	// Récupère un post via son id
	// ##############################################################################
	public function GetPostById($id) : Post
	{
		$datas 	= $this->db->query('SELECT Titre, Id, Picture, Chapo, Auteur, Contenu, 
		(SELECT COUNT(PostsCommentaire.Id) FROM PostsCommentaire WHERE PostsCommentaire.IdPost = BlogPosts.Id) AS NbrComment  FROM BlogPosts WHERE Id = :id', array(':id' => $id));
		$post = new Post($datas);
		
		return $post;
	}
}