<?php
namespace App\Model;
use PDO;

class Database
{
	private $database;
	private static $_instance;
	
	public function __construct()
	{
	}
		
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Database();
		}
				
		return self::$_instance;
	}
	
	private function getDb()
	{
		if($this->database === null)
		{
			$this->database = new PDO('mysql:dbname='.SQL_DB.';host='.SQL_HOST, SQL_USER, SQL_MDP);
			$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
		return $this->database;
	}
	
	public function query($query, $arg = array())
	{
		$req = $this->getDb()->prepare($query);
		$req->execute($arg);
		$datas = $req->fetchAll(PDO::FETCH_OBJ);
		
		return $datas;
	}
	
	public function Update($query, $arg = array())
	{
		$req = $this->getDb()->prepare($query);
		$req->execute($arg);
	}
	
	public function Insert($query, $arg = array())
	{
		$req = $this->getDb()->prepare($query);
		$req->execute($arg);
	}
	
	public function Del($query, $arg = array())
	{
		$req = $this->getDb()->prepare($query);
		$req->execute($arg);
	}
}
?>