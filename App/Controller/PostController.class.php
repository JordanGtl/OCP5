<?php
namespace App\Controller;

use Core\Controller\Controller;
use App\Controller\AppController;
use App\Model\Posts;
use App\Model\Comments;
use StdClass;

class PostController extends AppController
{	
	private $model;
	private $modelcom;

	public function __construct()
	{
		parent::__construct();
		$this->model = new Posts();
		$this->modelcom = new Comments();
	}

	// ##############################################################################
	// Controller de la page index.php
	// ##############################################################################
	public function Index()
	{		
		$posts = $this->model->getLastPost();
		
		for($i = 0; $i < 6; ++$i)
		{
			if(!isset($posts[$i]))
			{
				$prov = new stdclass();
				$prov->Picture = 'https://gtl-studio.com/images/coming.png';
				$prov->Link = '#';
				$prov->Id = 0;
				
				$posts[$i] = $prov;
				continue;
			}

			$posts[$i]->Link = 'index.php?page=1&id='.$posts[$i]->Id;
		}

		$args = array('posts' => $posts, 'statut' => 'success', 'message' => '');

        if(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) != null)
            $args = array_merge($args, $this->model->PostContact());
		
		$this->Render('home.html', $args);
	}
	
	// ##############################################################################
	// Controller de la page posts (listes)
	// ##############################################################################
	public function ShowPostsList()
	{			
		$this->Render('Posts/postsliste.html', array('posts' => $this->model->getLastPost()));
	}
	
	// ##############################################################################
	// Controller de la page post (lecture d'un posts)
	// ##############################################################################
	public function ShowPost()
	{
		$ident 		= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
		$username 	= (isset($_SESSION['nom']) && isset($_SESSION['prenom'])) ? $_SESSION['nom'].' '.$_SESSION['prenom'] : '';
		$args		= array('posts' => $this->model->GetPostById($ident), 'comments' => $this->modelcom->getCommentListByPostId($ident), 'username' => $username);

    	if(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) != null)
			$args = array_merge($args, $this->modelcom->Add());
		
		$this->Render('Posts/posts.html', $args);
	}
}