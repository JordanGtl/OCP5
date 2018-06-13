<?php
namespace App\Controller;

use Core\Controller\Controller;
use App\Controller\AppController;
use App\Model\Admin;


class AdminController extends AppController
{
	private $admin;
	private $posts;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		parent::__construct();
		$this->admin =  Admin::getInstance();
	}
	
	// ##############################################################################
	// Controller d'affichage de la page d'accueil de l'administration
	// ##############################################################################
	public function ShowHome()
	{	
		$args = $this->admin->GetHomeStat();
		
		$this->Render('Admin/home.html', $args);
	}	
	
	// ##############################################################################
	// Controller d'affichage de la page gestion post (admin)
	// ##############################################################################
	public function ShowAdminPost()
	{
		if(isset($_GET['edit']))
		{
			if(count($_POST) > 0)
			{
				$args = $this->admin->SetPostsEdit();
				$this->Render('Admin/postsedit.html', $args);
			}
			else
			{
				$args = $this->admin->GetPostsEdit();
				$this->Render('Admin/postsedit.html', $args);
			}
		}
		else if(isset($_GET['trash']))
		{
			if(isset($_GET['confirm']))
			{
				$args = $this->admin->setPostsDel();	
				$this->Render('Admin/postsdel.html', $args);
			}
			else
			{
				$args = $this->admin->GetPostsDel();
				$args['confirm']= false;
				$this->Render('Admin/postsdel.html', $args);
			}
		}
		else
		{
			$args = $this->admin->GetPostsList();
			$this->Render('Admin/posts.html', $args);
		}
	}
	
	public function ShowAddPost()
	{
		$args = $this->admin->getPostAdd();
		
		if(count($_POST) > 0)
			$args = array_merge($args, $this->admin->setPostAdd());
				
		$this->Render('Admin/postsadd.html', $args);
	}
	
	public function ShowWaitCom()
	{
		$args = $this->admin->getWaitCom();
		
		if(isset($_GET['confirm']))
		{
			$args = array_merge($args, $this->admin->AcceptComment());
			$this->Render('Admin/comedit.html', $args);
		}
		else if(isset($_GET['trash']))
		{
			$args = array_merge($args, $this->admin->RefuseComment());
			$this->Render('Admin/comedit.html', $args);
		}
		else
		$this->Render('Admin/comwait.html', $args);
	}
}
?>