<?php
namespace Core\Html;

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
		$_SESSION['token'] = md5(uniqid(rand(), TRUE));
		return $_SESSION['token'];
	}
}
?>