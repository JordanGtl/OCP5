<?php
namespace App\Model;

use \App\Entity\Utilisateur;
use \App\Entity\Post;
use \App\Entity\Commentaire;


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
	
	// ##############################################################################
	// Fonction de listage des posts
	// ##############################################################################
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
			return array('authorize' => false, 'posts' => array());
	}
	
	// ##############################################################################
	// Fonction d'édition des posts (getter)
	// ##############################################################################
	public function GetPostsEdit() : array
	{
		if($this->CheckRank())
		{
			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur
			WHERE BlogPosts.Id = ?', array(intval($_GET['id'])));
			
			if(count($req) == 0)
                return array('authorize' => false);
						
			$post = new Post((array)$req[0]);			
						
			return array('authorize' => true, 'post' => $post);
		}
		else
			return array('authorize' => false);
	}
	
	// ##############################################################################
	// Fonction d'édition des posts (setter)
	// ##############################################################################
	public function SetPostsEdit() : array
	{
		if($this->CheckRank())
		{
			$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
			$titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
			$contenu = nl2br(filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING));
			
			if($titre == "")
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Aucun titre n\'est renseigné');
			
			if($contenu == "")
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Aucun contenu n\'est renseigné');
			
			if(strlen(filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING)) > CHAPO_CHAR_LIMIT)
				$chapo = substr(filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING), 0, CHAPO_CHAR_LIMIT);
			else
				$chapo = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);

			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur
			WHERE BlogPosts.Id = ?', array($id));
			
			if(count($req) == 0)
				die('Le contenu n\'existe pas');	
			
			$post = new Post($req);

			$this->db->update('UPDATE BlogPosts SET Titre = ?, Contenu = ?, Chapo = ? WHERE Id = ?', array($titre, $contenu, $chapo, intval($_GET['id'])));
						
			return array('authorize' => true, 'statut' => 'success', 'message' => 'Le posts "'.$post->getTitle().'" a été modifié avec succès');
		}
		else
			return array('authorize' => false);
	}
	
	// ##############################################################################
	// Récupération des info page supression de post (getter)
	// ##############################################################################
	public function GetPostsDel() : array
	{
		if($this->CheckRank())
		{
			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur
			WHERE BlogPosts.Id = ?', array(intval($_GET['id'])));
			
			if(count($req) == 0)
				return array('statut' => 'fail', 'message' => 'Le contenu n\'existe pas');
			
			return array('authorize' => true, 'statut' => 'success', 'message' => 'Vous allez supprimer le post <b>'.$req[0]->Titre.'</b>, souhaitez-vous continuer', 'linktext' => 'Confirmer la supression', 'link' => 'index.php?page='.$_GET['page'].'&trash=1&id='.$_GET['id'].'&confirm=1');
		}
		else
			return array('authorize' => false);
	}
	
	// ##############################################################################
	// Récupération des info page supression de post (setter)
	// ##############################################################################
	public function setPostsDel() : array
	{
		if($this->CheckRank())
		{
			$req = $this->db->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Contenu, BlogPosts.Chapo, Utilisateurs.NomDeCompte AS Auteur, BlogPosts.DerniereModification AS Date 
			FROM BlogPosts
			INNER JOIN Utilisateurs ON Utilisateurs.Id = BlogPosts.Auteur
			WHERE BlogPosts.Id = ?', array(intval($_GET['id'])));
			
			if(count($req) == 0)
				return array('statut' => 'fail', 'message' => 'Le contenu n\'existe pas');
			
			$this->db->Del('DELETE FROM BlogPosts WHERE Id = ?', array(intval($_GET['id'])));
			
			return array('authorize' => true, 'statut' => 'success', 'message' => 'Le post <b>'.$req[0]->Titre.'</b> a été supprimé', 'linktext' => 'Continuer', 'link' => 'index.php?page=10');
		}
		else
			return array('authorize' => false);
	}
	
	// ##############################################################################
	// Récupération des info template d'ajout d'un post (getter)
	// ##############################################################################
	public function getPostAdd()
	{
		if($this->CheckRank())
		{
			return array('authorize' => true, 'auteur' => $_SESSION['login']);
		}
		else
			return array('authorize' => false);
	}
	
	
	// ##############################################################################
	// Fonction permettant d'ajouter un posts (setter)
	// ##############################################################################
	public function setPostAdd()
	{
		if($this->CheckRank())
		{			
			$titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
			$contenu = nl2br(filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING));
			
			if(strlen(filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING)) > CHAPO_CHAR_LIMIT)
				$chapo = substr(filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING), 0, CHAPO_CHAR_LIMIT);
			else
				$chapo = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
			
			if($titre == '')
				return array('statut' => 'fail', 'message' => 'Aucun titre n\'est renseigné');
			
			if($contenu == '')
				return array('statut' => 'fail', 'message' => 'Aucun contenu n\'est renseigné');
			
			if(!isset($_FILES['picture']))
				return array('statut' => 'fail', 'message' => 'Aucune image n\'est renseigné');
			
			$image = base64_encode(file_get_contents($_FILES['picture']['tmp_name']));
			$image = 'data:'.$_FILES['picture']['type'].';base64, '.$image;
			
			$this->db->insert('INSERT INTO BlogPosts (Titre, DerniereModification, Picture, Auteur, Chapo, Contenu) VALUES (?, ?, ?, ?, ?, ?)', array($titre, date('Y-m-d H:i:s', time()), $image, $_SESSION['id'], $chapo, $contenu));
			
			# succes
			return array('statut' => 'success', 'message' => 'Le post a été publié sur le site');
		}
		else
			return array('statut' => 'fail', 'message' => 'Vous ne disposez pas des autorisations nécessaire');
	}
	
	// ##############################################################################
	// Fonction qui liste les commentaires (getter)
	// ##############################################################################
	public function getWaitCom()
	{
		if($this->CheckRank())
		{
			$req = $this->db->query('SELECT PostsCommentaire.Id, PostsCommentaire.IdPost, CONCAT(Utilisateurs.Nom , " " , Utilisateurs.Prenom) AS Auteur, PostsCommentaire.Date, PostsCommentaire.Contenu, BlogPosts.Titre AS ParentPost
			FROM PostsCommentaire
			INNER JOIN BlogPosts ON BlogPosts.Id = PostsCommentaire.IdPost
			INNER JOIN Utilisateurs ON Utilisateurs.Id = PostsCommentaire.Auteur
			WHERE PostsCommentaire.Statut = 0');
			
			$coms = array();
			
			foreach($req as $data)
			{
				$coms[] = new Commentaire($data);
			}
			
			return array('authorize' => true, 'liste' => $coms);
		}
		else
			return array('authorize' => false, 'liste' => array());
	}
	
	// ##############################################################################
	// Fonction qui permet d'accepter un commentaire utilisateur (setter)
	// ##############################################################################
	public function AcceptComment()
	{
		if($this->CheckRank())
		{
			$id = $_GET['id'] ?? 0;
			
			$req = $this->db->query('SELECT PostsCommentaire.Id
				FROM PostsCommentaire
				WHERE PostsCommentaire.Id = ?', array($id));
			
			if($id == 0)
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Aucun commentaire n\'est sélectionné');
			
			if(count($req) == 0)
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Le commentaire sélectionné n\'existe pas');
			
			$this->db->update('UPDATE PostsCommentaire SET Statut = 1 WHERE Id = ?', array($id));

			return array('authorize' => true, 'statut' => 'success', 'message' => 'Le commentaire est publié avec succès');
		}
		else
			return array('authorize' => false);
	}
	
	// ##############################################################################
	// Fonction qui permet de refuser un commentaire utilisateur (setter)
	// ##############################################################################
	public function RefuseComment()
	{
		if($this->CheckRank())
		{
			$id = $_GET['id'] ?? 0;
			
			$req = $this->db->query('SELECT PostsCommentaire.Id
				FROM PostsCommentaire
				WHERE PostsCommentaire.Id = ?', array($id));
			
			if($id == 0)
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Aucun commentaire n\'est sélectionné');
			
			if(count($req) == 0)
				return array('authorize' => true, 'statut' => 'fail', 'message' => 'Le commentaire sélectionné n\'existe pas');
			
			$this->db->update('UPDATE PostsCommentaire SET Statut = 2 WHERE Id = ?', array($id));

			return array('authorize' => true, 'statut' => 'success', 'message' => 'Le commentaire est refusé avec succès');
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