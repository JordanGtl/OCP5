<?php
namespace Core\Controller;

class Controller
{
	private $parseur;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		$this->parseur = \Core\Html\Template::getInstance();
	}
	
	// ##############################################################################
	// Fonction render d'affichage d'une page (fait appel au parseur HTML)
	// ##############################################################################
	public function Render($file, $args = array())
	{
		$this->parseur->getPage($file, $args);
	}
}
?>