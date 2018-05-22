<?php
class Frontend
{	
	private $db;
	private $parseur;
	
	public function __construct($bdd, $parseur)
	{
		$this->bdd = $bdd;
		$this->parseur = $parseur;
	}

	public function GetPage()
	{
		$db = $this->bdd;		
		$page = $_GET['page'] ?? 0;
		$parseur = $this->parseur;
		
		switch($page)
		{
			case 0: // Accueil
			{
				include($_SERVER['DOCUMENT_ROOT'].'/model/Home.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'/controler/frontend/home.php');
				return $this->parseur->ParsePage($_SERVER['DOCUMENT_ROOT'].'/view/frontend/home.html');
				break;
			}
			case 1: // Posts
			{
				include($_SERVER['DOCUMENT_ROOT'].'model/Posts.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'model/Commentaire.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'controler/frontend/posts.php');
				return $this->parseur->ParsePage($_SERVER['DOCUMENT_ROOT'].'/view/frontend/posts.html');
				break;
			}
			case 3: // Connexion
			{
				include($_SERVER['DOCUMENT_ROOT'].'controler/frontend/connexion.php');
				return $this->parseur->ParsePage($_SERVER['DOCUMENT_ROOT'].'/view/frontend/connexion.html');
				break;
			}
			case 4: // Inscription
			{				
				include($_SERVER['DOCUMENT_ROOT'].'controler/frontend/inscription.php');
				return $this->parseur->ParsePage($_SERVER['DOCUMENT_ROOT'].'/view/frontend/inscription.html');
				break;
			}
			case 6: // Administration (requis droit admin)
			{	
				include($_SERVER['DOCUMENT_ROOT'].'model/Administration.class.php');
				include($_SERVER['DOCUMENT_ROOT'].'view/backend/administration.php');
				include($_SERVER['DOCUMENT_ROOT'].'controler/backend/administration.html');
				break;
			}
			case 5: // Erreur 404
			default:
			{
				include($_SERVER['DOCUMENT_ROOT'].'view/frontend/404.html');
				break;
			}
		}
	}
}
?>