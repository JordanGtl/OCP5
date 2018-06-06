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
			}
			else
			{
				$posts[$i]->Link = 'index.php?page=1&id='.$posts[$i]->Id;
			}
		}
		
		$this->Render('home.html', array('posts' => $posts));
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
		$id 		= intval($_GET['id']);
		$username 	= (isset($_SESSION['nom']) && isset($_SESSION['prenom'])) ? $_SESSION['nom'].' '.$_SESSION['prenom'] : '';
		$args		= array('posts' => $this->model->GetPostById($id), 'comments' => $this->modelcom->getCommentListByPostId($id), 'username' => $username);
		
		if(count($_POST) > 0)
			$args = array_merge($args, $this->modelcom->Add());
		
		$this->Render('Posts/posts.html', $args);
	}
}