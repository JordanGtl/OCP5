<?php
class Frontend
{	
	private $db;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}

	public function GetPage()
	{
		$db = $this->bdd;		
		$page = $_GET['page'] ?? 0;
		
		switch($page)
		{
			case 0: // Accueil
			{
				include($_SERVER['DOCUMENT_ROOT'].'/model/Home.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'/controler/frontend/home.php');
				include($_SERVER['DOCUMENT_ROOT'].'/view/frontend/home.php');
				break;
			}
			case 1: // Posts
			{
				include($_SERVER['DOCUMENT_ROOT'].'model/Posts.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'model/Commentaire.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'controler/frontend/posts.php');
				include($_SERVER['DOCUMENT_ROOT'].'view/frontend/posts.php');
				break;
			}
			case 3: // Connexion
			{
				include($_SERVER['DOCUMENT_ROOT'].'model/Membre.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'controler/frontend/connexion.php');
				include($_SERVER['DOCUMENT_ROOT'].'view/frontend/connexion.php');
				break;
			}
			case 4: // Inscription
			{
				include($_SERVER['DOCUMENT_ROOT'].'model/Membre.class.php');				
				include($_SERVER['DOCUMENT_ROOT'].'controler/frontend/inscription.php');
				include($_SERVER['DOCUMENT_ROOT'].'view/frontend/inscription.php');
				break;
			}
			case 6: // Administration (requis droit admin)
			{	
				include($_SERVER['DOCUMENT_ROOT'].'model/Administration.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'view/backend/administration.php');
				include($_SERVER['DOCUMENT_ROOT'].'controler/backend/administration.php');
				break;
			}
			case 5: // Erreur 404
			default:
			{
				include($_SERVER['DOCUMENT_ROOT'].'view/frontend/404.php');
				break;
			}
		}
	}
	
	public function IsLogged() : bool
	{
		return (isset($_SESSION['login'])) ? true : false;
	}
}
?>