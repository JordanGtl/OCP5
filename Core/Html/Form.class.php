<?php
namespace Core\Html;

use Core\Objects\Session;

class Form
{
	private static $_instance;
	
	// ##############################################################################
	// Retourne l'instance de classe
	// ##############################################################################
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Form();
		}
		
		return self::$_instance;
	}
	
	// ##############################################################################
	// Génère un token pour les faille CSRF de formulaire
	// ##############################################################################
	public function generateToken() : string
	{
		$session = new Session();
		$session->vars['token'] = md5(uniqid(rand(), TRUE));
		
		return $session->vars['token'];
	}
}
?>