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
			$args = $this->admin->GetPostsDel();
			$this->Render('Admin/postsdel.html', $args);
		}
		else
		{
			$args = $this->admin->GetPostsList();
			$this->Render('Admin/posts.html', $args);
		}
		
		
	}
}
?>