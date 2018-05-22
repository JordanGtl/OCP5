<?php
class Commentaire
{
	private $bdd;
	
	public function __construct(PDO $bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function GetByPostId(int $id)
	{
		if($id == 0)
			throw new Exception('Aucun id n\'est indiqué pour la fonction Commentaire->GetByPostId(); - Valeur de $id : '.$id);
	}
}
?>