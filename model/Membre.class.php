<?php
class Membres
{
	private $bdd;
	
	public function __construct(PDO $bdd)
	{
		$this->bdd = $bdd;
	}
}
?>