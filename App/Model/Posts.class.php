<?php
namespace App\Model;
use App\Model\Membres;
use App\Entity\Post;

class Posts
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
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Posts();
		}
		
		return self::$_instance;
	}

	public function PostContact()
    {
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

        if($token != $_SESSION['token'])
            return array('statut' => 'fail', 'message' => 'Toekn de vérification du formulaire incorrect');

        if($email == '')
            return array('statut' => 'fail', 'message' => 'Aucune adresse email renseignée');

        if($name == '')
            return array('statut' => 'fail', 'message' => 'Aucun nom renseigné');

        if($message == '')
            return array('statut' => 'fail', 'message' => 'Aucun message renseigné');

        if(!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL))
            return array('statut' => 'fail', 'message' => 'L\'adresse email n\'est pas valide');

        $membre = new Membres();
        $membre->SendContactMail($email, $name, $message);

        return array('statut' => 'success', 'message' => 'Email envoyé, vous recevrez une réponse dans les plus bref délais');
    }

	// ##############################################################################
	// Récupère les dernier posts
	// ##############################################################################
	public function getLastPost() : array
	{
		$req 	= $this->database->query('SELECT BlogPosts.Id, BlogPosts.Titre, BlogPosts.Picture, BlogPosts.Chapo, BlogPosts.Auteur, 
		(SELECT COUNT(PostsCommentaire.Id) FROM PostsCommentaire WHERE PostsCommentaire.IdPost = BlogPosts.Id AND PostsCommentaire.Statut = 1) AS NbrComment 
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
	public function GetPostById($ident) : Post
	{
		$datas 	= $this->database->query('SELECT Titre, Id, Picture, Chapo, Auteur, Contenu, 
		(SELECT COUNT(PostsCommentaire.Id) FROM PostsCommentaire WHERE PostsCommentaire.IdPost = BlogPosts.Id AND PostsCommentaire.Statut = 1) AS NbrComment FROM BlogPosts WHERE Id = :id', array(':id' => $ident));
		$post = new Post($datas);
		
		return $post;
	}
}