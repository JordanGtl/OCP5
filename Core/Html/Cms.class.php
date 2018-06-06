<?php
namespace Core;

class Cms
{
	public function __construct()
	{
		
	}
	
	public function Load()
	{
		# Autoloader (Core)
		require (ROOT.'/Core/Autoloader.class.php');
		\Cms\Core\Autoloader::Register();
		
		# set variables
		$this->SetServerVariable();
	}
	
	public SetServerVariable()
	{
		$_SERVER['REMOTE_ADDR'] = (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR']);
	}
	
}