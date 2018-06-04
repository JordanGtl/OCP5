<?php
use Core\Html\Template;
use App\Model\Membres;

class App
{
	private static $_instance;
	
	public function __construct()
	{
		
	}
	
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$instance = new Template();
		}
		
		return self::$_instance;
	}
	
	public static function Load()
	{
		# Initialisation des sessions
		session_start();
		
		# Autoloader (Application)
		require (ROOT.'/App/Autoloader.class.php');
		\App\Autoloader::Register();
		
		# Autoloader (Core)
		require (ROOT.'/Core/Autoloader.class.php');
		\Core\Autoloader::Register();
	}
	
	public static function renderTemplate()
	{
		$parseur = Template::getInstance();
		$page 	 = $_GET['page'] ?? 0;
		$parseur->getTemplate(ROOT.'/App/Views/Template/default.html');

		# Pages
		switch(intval($page))
		{
			case 0: // Accueil
			{
				$controller = new \App\Controller\PostController();
				$controller->Index();				
				break;
			}
			case 1: // Posts
			{		
				$controller = new \App\Controller\PostController();
				
				if(!isset($_GET['id']))
					$controller->ShowPostsList();
				else
					$controller->ShowPost();
				break;
			}
			case 3: // Connexion
			{
				$controller = new \App\Controller\UsersController();
				$controller->ShowLogin();
				break;
			}
			case 4: // Inscription
			{
				$controller = new \App\Controller\UsersController();
				$controller->ShowRegister();
				break;
			}
			case 6: // Activation d'un compte
			{
				$controller = new \App\Controller\UsersController();
				$controller->ActivateAccount();
				break;
			}
			case 7: // Administration (requis droit admin)
			{	
				return $parseur->getPage('Admin/home.html');
				break;
			}
			case 8: // Mot de passe perdu
			{	
				$controller = new \App\Controller\UsersController();
				$controller->ShowPasswordLost();
				break;
			}
			case 9: // Déconnexion
			{	
				$controller = new \App\Controller\UsersController();
				$controller->Logout();
				break;
			}
			case 5: // Erreur 404
			default:
			{
				return $parseur->getPage('Errors/404.html');
				break;
			}
		}
	}
}
?>