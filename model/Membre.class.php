<?php
class Membres
{
	private $bdd;
	
	public function __construct(PDO $bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function IsLogged() : bool
	{
		return (isset($_SESSION['login'])) ? true : false;
	}
}
?>