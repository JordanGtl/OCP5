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

        $args = [];

		if(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) != null)
		    $args = $this->membre->AjoutMembre();

		$this->Render('Users/inscription.html', $args);
	}
	
	// ##############################################################################
	// Controller page de connexion
	// ##############################################################################
	public function ShowLogin()
	{
	    $args = array();

		if(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) != null)
		    $args = $this->membre->AuthUser();
		
		$this->Render('Users/connexion.html', $args);
	}
	
	// ##############################################################################
	// Controller page de connexion
	// ##############################################################################
	public function ShowPasswordLost()
	{		
		if(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING) == null)
		{
			$args = (filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) != null) ? $this->membre->PasswordLost() : [];
			$this->Render('Users/mdplost.html', $args);
		}
		else
		{
			$user = $this->membre->VerifPasswordToken($_GET['token']);

            if(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) != null)
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
		$retour = (filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING) != null) ? $this->membre->ActiveAccount() : array('message' => 'Aucun token d\'activation n\'est renseigné');

		$this->Render('Users/activate.html', $retour);
	}

    // ##############################################################################
    // Controller d'affichage de la page mon compte
    // ##############################################################################
    public function MyAccount()
    {
        $args = $this->membre->getMyAccountPage();

        if(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING) != null)
            $args = array_merge($args, $this->membre->setMyAccountPage());

        $this->Render('Users/myaccount.html', $args);
    }
}