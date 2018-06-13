<?php
namespace Core\Controller;

use Core\Html\Template;

class Controller
{
	private $parseur;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		$this->Init();
	}
	
	// ##############################################################################
	// Initialisation des propriétés
	// ##############################################################################
	public function Init()
	{
		$this->parseur = Template::getInstance();
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