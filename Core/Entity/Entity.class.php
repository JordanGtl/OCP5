<?php
namespace Core\Entity;
use \App\Model\Database;

class Entity
{
	protected $exist;

	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		$this->exist = false;
	}
	
	// ##############################################################################
	// Création des variable de classes avec le résultat de la requete sql
	// ##############################################################################
	protected function Hydrate($result)
	{
		if((is_array($result) && isset($result[0])) || is_object($result))
			$this->exist = true;
		
		if(is_array($result) && isset($result[0]))
        {
			$result = (array)$result[0];
		}
		elseif(is_object($result))
		{
			$result = (array)$result;
		}
		
		foreach($result as $key => $data)
		{
			$this->{$key} = $data;
		}
	}
	
	// ##############################################################################
	// Vérifie si les données on bien été trouvé dans la base de données
	// ##############################################################################
	public function Exist()
	{
		return $this->exist;
	}
}
?>