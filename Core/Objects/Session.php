<?php
namespace Core\Objects;

class Session
{
    public $vars;

    public function __construct() 
	{
        $this->vars = &$_SESSION; //this will still trigger a phpmd warning
    }
}