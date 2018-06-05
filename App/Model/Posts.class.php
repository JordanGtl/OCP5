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
	public function getLastPost()
	{
		$req 	= $this->db->query('SELECT Id, Titre, Picture, Chapo, Auteur FROM BlogPosts LIMIT 0,6');
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
	public function GetPostById($id)
	{
		$datas 	= $this->db->query('SELECT * FROM BlogPosts WHERE Id = :id', array(':id' => $id));
		
		return $datas;
	}
}