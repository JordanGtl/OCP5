<?php
namespace App\Controller;

use Core\Controller\Controller;
use App\Controller\AppController;
use App\Model\Membres;

use App\Entity\Utilisateur;

class UsersController extends AppController
{
	private $membre;
	
	// ##############################################################################
	// Parsage et affichage des includes html
	// ##############################################################################
	public function __construct()
	{
		parent::__construct();
		$this->membre =  Membres::getInstance();
	}
	
	// ##############################################################################
	// Controller page d'inscription
	// ##############################################################################
	public function ShowRegister()
	{
		$args = (count($_POST) > 0) ? $this->membre->AjoutMembre() : [];
		
		$this->Render('Users/inscription.html', $args);
	}
	
	// ##############################################################################
	// Controller page de connexion
	// ##############################################################################
	public function ShowLogin()
	{
		$args = (count($_POST) > 0) ? $this->membre->AuthUser(): [];
		
		$this->Render('Users/connexion.html', $args);
	}
}
?>