<?php
class Posts
{
	private $bdd;
	
	# Constructeur de classe
	public function __construct(PDO $bdd)
	{
		$this->bdd = $bdd;
	}
	
	# Récupération de la liste des blog posts
	public function GetList()
	{
		
	}
	
	# Récupération d'un blog post via son id
	public function GetById(int $id)
	{
		if($id == 0)
			throw new Exception('Aucun id n\'est indiqué pour la fonction Posts->GetById(); - Valeur de $id : '.$id);
	}
}
?>