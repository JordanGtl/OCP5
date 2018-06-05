<?php
namespace App\Controller;

use Core\Controller\Controller;
use App\Controller\AppController;
use App\Model\Membres;
use \App\Model\Database;

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
		$this->db = Database::getInstance();
		
		// $user = $this->db->query('SELECT Id FROM Utilisateurs WHERE Id = ?', array(12));
		// $user = new Utilisateur($user);
		// var_dump($user->Exist());
		
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
	
	// ##############################################################################
	// Controller page de connexion
	// ##############################################################################
	public function ShowPasswordLost()
	{		
		if(!isset($_GET['token']))
		{
			$args = (count($_POST) > 0) ? $this->membre->PasswordLost() : [];
			$this->Render('Users/mdplost.html', $args);
		}
		else
		{
			$user = $this->membre->VerifPasswordToken($_GET['token']);
						
			if(count($_POST) > 0)
				$args = $this->membre->SetLostPassword();
			else
				$args = array();
			
			# Gere la condition if du parseur
			$args['result'] = $user->Exist();
			
			$args['user'] = $user->getFullName();
			
			$this->Render('Users/mdpedit.html', $args);
		}
	}
	
	// ##############################################################################
	// Controller de déconnexion
	// ##############################################################################
	public function Logout()
	{
		session_destroy();
		header('location:index.php');
	}
	
	// ##############################################################################
	//Controller d'activation d'un compte
	// ##############################################################################
	public function ActivateAccount()
	{
		$retour = (isset($_GET['token'])) ? $this->membre->ActiveAccount() : array('message' => 'Aucun token d\'activation n\'est renseigné');
		
		$this->Render('Users/activate.html', $retour);
	}
}
?>