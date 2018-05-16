<?php
class Core
{
	public function GetPage()
	{
		$page = $_GET['page'] ?? 0;
		
		switch($page)
		{
			case 0: // Accueil
			{
				include('pages/home.php');
				break;
			}
			case 1: // Posts
			{
				include('pages/posts.php');
				break;
			}
			case 2: // Contact
			{
				include('pages/contact.php');
				break;
			}
			case 3: // Connexion
			{
				include('pages/connexion.php');
				break;
			}
			case 4: // Inscription
			{
				include('pages/inscription.php');
				break;
			}
			case 5: // Erreur 404
			default:
			{
				include('pages/404.php');
				break;
			}
		}
	}
}
?>