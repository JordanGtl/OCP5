<?php
namespace App\Model;

use \App\Entity\Utilisateur;
use \App\Entity\Post;


class Admin
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
			self::$_instance = new Admin();
		}
		
		return self::$_instance;
	}
	
	// ##############################################################################
	// On affiche la page d'accueil de l'administration
	// ##############################################################################
	public function GetHomeStat() : array
	{
		if($this->CheckRank())
		{
			$users = $this->db->query('SELECT Count(Id) AS Nbr FROM Utilisateurs');
			$posts = $this->db->query('SELECT Count(Id) AS Nbr FROM BlogPosts');
			$coms = $this->db->query('SELECT Count(Id) AS Nbr FROM PostsCommentaire WHERE Statut = 1');
			$wait = $this->db->query('SELECT Count(Id) AS Nbr FROM PostsCommentaire WHERE Statut = 0');
			
			return array('authorize' => true, 'usersnbr' => $users[0]->Nbr, 'postsnbr' => $posts[0]->Nbr, 'comsnbr' => $coms[0]->Nbr, 'waitnbr' => $wait[0]->Nbr);
		}
		else
		{
			return array('authorize' => false);
		}
	}
	
	public function GetPostsList() : array
	{
		if($this->CheckRank())
		{
			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur');
			
			$posts = array();
		
			foreach($req as $data)
			{		
				$post = new Post((array)$data);
				$post->LinkEdit = 'index.php?page=10&edit=1&id='.$post->Id;
				$post->LinkTrash = 'index.php?page=10&trash=1&id='.$post->Id;
				$posts[] = $post;
			}
			
			return array('authorize' => true, 'posts' => $posts);
		}
		else
			return array('authorize' => false);
	}
	
	public function GetPostsEdit() : array
	{
		if($this->CheckRank())
		{
			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur
			WHERE BlogPosts.Id = ?', array(intval($_GET['id'])));
			
			if(count($req) == 0)
				die('Le contenu n\'existe pas');
						
			$post = new Post((array)$req[0]);			
						
			return array('authorize' => true, 'post' => $post);
		}
		else
			return array('authorize' => false);
	}
	
	public function SetPostsEdit() : array
	{
		if($this->CheckRank())
		{
			$id = intval($_GET['id']);	
			$titre = htmlentities($_POST['titre']);
			$contenu = nl2br(htmlentities($_POST['contenu']));
			
			if($titre == "")
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Aucun titre n\'est renseigné');
			
			if($contenu == "")
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Aucun contenu n\'est renseigné');
			
			if(strlen(htmlentities($_POST['contenu'])) > CHAPO_CHAR_LIMIT)
				$chapo = substr(htmlentities($_POST['contenu']), 0, CHAPO_CHAR_LIMIT);
			else
				$chapo = htmlentities($_POST['contenu']);

			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur
			WHERE BlogPosts.Id = ?', array(intval($_GET['id'])));
			
			if(count($req) == 0)
				die('Le contenu n\'existe pas');	
			
			$post = new Post($req);

			$this->db->update('UPDATE BlogPosts SET Titre = ?, Contenu = ?, Chapo = ? WHERE Id = ?', array($titre, $contenu, $chapo, intval($_GET['id'])));
						
			return array('authorize' => true, 'statut' => 'success', 'message' => 'Le posts "'.$post->getTitle().'" a été modifié avec succès');
		}
		else
			return array('authorize' => false);
	}
	
	public function GetPostsDel() : array
	{
		if($this->CheckRank())
		{
			
			
			return array('authorize' => true);
		}
		else
			return array('authorize' => false);
	}
	
	// ##############################################################################
	// Vérification des autorisation
	// ##############################################################################
	private function CheckRank() : bool
	{
		if(!isset($_SESSION['id']))
			return false;
		
		$sql = $this->db->query('SELECT Rang FROM Utilisateurs WHERE Id = ?', array($_SESSION['id'] ?? 0));
		$user = new Utilisateur($sql);
		
		return ($user->getRank() < 3) ? false : true;
	}
}
?>