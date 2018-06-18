<?php
namespace Core\Html;

use Exception;

class Template
{
	protected $html;
	protected $page;
	protected $template;
	protected $callback;
	public $classes;
	public $vars;
	private static $_instance;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		
	}
	
	// ##############################################################################
	// Retourne l'instance de classe
	// ##############################################################################
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Template();
		}
		
		return self::$_instance;
	}
	
	// ##############################################################################
	// Fonction de parsage du template
	// ##############################################################################
	public function getTemplate(string $file)
	{		
		if(file_exists($file))
		{
			$this->html = file_get_contents($file);
			$this->ParsePhp($this->html);
			return;
		}

		$this->html = "le fichier template <b>" . $file . "</b> n'existe pas";
	}
	
	// ##############################################################################
	// Fonction de parsage d'une page
	// ##############################################################################
	public function getPage(string $file, array $args = array())
	{
		$this->page = file_get_contents(ROOT.'/App/Views/'.$file);
		$this->vars = $args;
		
		$this->ParsePhp($this->page);
		$template = str_replace('{PAGES_DATA}', $this->page, $this->html);

		echo filter_var($template);
	}
	
	// ##############################################################################
	// Parsage d'un EACH html -> retour d'une fonction
	// {{FOREACH:maclasse->mafonction()}}
	// ##############################################################################
	private function ParseEachFunction(string &$html, array $result, int $index)
	{
		if(isset($result[0][$index]) && isset($result[1][$index]) && isset($result[2][$index]))
		{
			$datas = $result[2][$index];
			
			$function = explode('->', str_replace('()', '', $result[1][$index]));
		
			$boucle = $function[0]::getInstance()->{$function[1]}();
			$retour = '';
			
			foreach($boucle as $data)
			{
				$pattern = '/{{(.*)}}/U';
				preg_match_all($pattern, $datas, $resultsub);
				
				$prov = $datas;
				
				foreach($resultsub[1] as $regex)
				{
					$prov = str_replace('{{'.$regex.'}}', $data->{$regex}, $prov);
				}
				
				$retour .= $prov;
			}
			
			$html = preg_replace('/{{FOREACH:'.addslashes($result[1][$index]).'\(\)}}(.*){{\/FOREACH}}/mUs', $retour, $html);
		}		
	}
	
	// ##############################################################################
	// Parsage d'un EACH html -> retour d'une variable
	// {{FOREACH:mavariable}}
	// ##############################################################################
	private function ParseEachVar(string &$html, array $result, int $index)
	{
		$pattern = '/{{(.*)}}/U';
		preg_match_all($pattern, $result[2][$index], $resultsub);
		
		$boucle = $this->vars[$result[1][$index]];		
		$retour = '';
		
		foreach($boucle as $data)
		{
			$datas = $result[2][$index];
			$pattern = '/{{(.*)}}/U';
			preg_match_all($pattern, $datas, $resultsub);
			
			$prov = $datas;

			foreach($resultsub[1] as $regex)
			{
				if(strpos($regex, '()') === false)
				{
					$prov = str_replace('{{'.$regex.'}}', $data->{$regex}, $prov);
				}
				else
				{
					$regex = str_replace('()', '', $regex);
					$prov = str_replace('{{'.$regex.'()}}', $data->{$regex}(), $prov);
				}
			}
			
			$retour .= $prov;
		}
	
		$html = preg_replace('/{{FOREACH:'.addslashes($result[1][$index]).'}}(.*){{\/FOREACH}}/mUs', $retour, $html);
	}
	
	// ##############################################################################
	// Parsage d'une boucle foreach et traitement de ses variables
	// ##############################################################################
	private function ParseForEachGlobal(string &$html)
	{
		# Foreach on function
		$pattern = '/{{FOREACH:(.*)}}(.*){{\/FOREACH}}/mU';
		preg_match_all($pattern, str_replace(PHP_EOL, '', $html), $matches);
		
		$count = count($matches);
		for($i = 0; $i < $count; ++$i)
		{
			if(!isset($matches[1][$i]))
				continue;
			
			if(strpos($matches[1][$i], '->') === false)
				$this->ParseEachVar($html, $matches, $i);
			else
				$this->ParseEachFunction($html, $matches, $i);
		}
	}

	// ##############################################################################
	// Fonction de parsage d'une condition IF avec une variable
	// ##############################################################################
	protected function ParseConditionVarWithElse(string &$html, string $condition, string $ifdata, string $elsedata)
	{
		$pattern = '/({{IF:(.*)}})(.*?){{ELSE}}(.*){{ENDIF}}/smU';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		$count = count($matches[2]);
		
		for($i = 0; $i < $count; ++$i)
		{
			if(count($matches) >= 5)
			{
				$retour 			= $this->vars[$condition];
				$pattern 			= '{{IF:'.$condition.'}}'.$ifdata.'{{ELSE}}'.$elsedata.'{{ENDIF}}';
				$html 				= str_replace($pattern, ($retour) ? $ifdata : $elsedata, $html);
			}
		}
		
		$this->ParsePhp($html);
	}
	

	// ##############################################################################
	// Fonction de parsage d'une condition IF avec une fonction
	// ##############################################################################
	protected function ParseConditionFunctionWithElse(string &$html, string $condition, string $ifdata, string $elsedata)
	{
		$pattern = '/({{IF:(.*)}})(.*?){{ELSE}}(.*){{ENDIF}}/smU';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		$count = count($matches[2]);

		if(stripos($condition, 'Tp') !== false)
		{
			if('Tp->FormExist()' == $condition)
			{
				if($this->FormExist())
				{
					$pattern 			= '{{IF:Tp->FormExist()}}'.$ifdata.'{{ELSE}}'.$elsedata.'{{ENDIF}}';
					$html 				= str_replace($pattern, $ifdata, $html);
				}
				else
				{
					$pattern 			= '{{IF:Tp->FormExist()}}'.$ifdata.'{{ELSE}}'.$elsedata.'{{ENDIF}}';
					$html 				= str_replace($pattern, $elsedata, $html);
				}
			}
		}
		else
		{		
			for($i = 0; $i < $count; ++$i)
			{
				if(count($matches) >= 5)
				{		
					$conditioncut 	= explode('->', $condition);
							
					if(count($conditioncut) < 2)
						throw new Exception('La syntaxe de la condition IF est incorrect');
					
				
					$conditioncut[1] 	= str_replace('()', '', $conditioncut[1]);
					$retour 			= $conditioncut[0]::{$conditioncut[1]}();
					$pattern 			= '{{IF:'.$conditioncut[0].'->'.$conditioncut[1].'()}}'.$ifdata.'{{ELSE}}'.$elsedata.'{{ENDIF}}';
					$html 				= str_replace($pattern, ($retour) ? $ifdata : $elsedata, $html);
				}
			}
		}
		
		$this->ParsePhp($html);
	}
	
	// ##############################################################################
	// Fonction de parsage d'une condition IF (global)
	// ##############################################################################
	protected function ParseConditionWithElse(string &$html)
	{
		$pattern = '/({{IF:(.*)}})(.*?){{ELSE}}(.*){{ENDIF}}/smU';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		$count = count($matches[2]);
		
		for($i = 0; $i < $count; ++$i)
		{
			if(count($matches) >= 5)
			{
				$condition 		= $matches[2][$i][0];
				$if 			= $matches[3][$i][0];
				$else 			= $matches[4][$i][0];	
				
				if(strpos($matches[2][$i][0], '->') === false)
				{
					$this->ParseConditionVarWithElse($html, $condition, $if, $else);
				}
				else
				{
					$this->ParseConditionFunctionWithElse($html, $condition, $if, $else);
				}
			}
		}
	}
	
	// ##############################################################################
	// Parsage de l'appel d'une fonction d'une classe
	// ##############################################################################
	private function ParseClassFunction(string &$html)
	{
		$pattern = '/\{\{(.*)->(.*)\(\)\}\}/U';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		
		if(isset($matches[1]) && isset($matches[2]))
		{	
			$count = count($matches[2]);
			for($i = 0; $i < $count; ++$i)
			{
				$class 		= $matches[1][$i][0];
				$function 	= $matches[2][$i][0];							
				$retour 	= (!class_exists($class)) ?  $this->vars[$class]->$function() : $class::getInstance()->$function();	
				
				$html 		= str_replace('{{'.$class.'->'.$function.'()}}', $retour, $html);
			}
		}
	}
	
	// ##############################################################################
	// Parsage d'une fonction procédurale
	// ##############################################################################
	private function ParseFunction(string &$html)
	{
		$pattern = '/\{\{(.*)\(\)\}\}/U';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		
		if(isset($matches[1]))
		{	
			$count = count($matches[1]);
			for($i = 0; $i < $count; ++$i)
			{
				$function = $matches[1][$i][0];
				
				if(!function_exists($function))
					throw new Exception('La fonction {'.$function.'()} n\'existe pas dans le parseur');

				$retour = call_user_func_array($function, array());				
				$html 	= str_replace('{{'.$function.'()}}', $retour, $html);
			}
		}
	}
	
	// ##############################################################################
	// Parsage et affichages des variables
	// ##############################################################################
	private function ParseVariable(string &$html)
	{
		$pattern = '/\{\{(.*)\}\}/U';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		
		if(isset($matches[1]))
		{	
			$count = count($matches[1]);
			for($i = 0; $i < $count; ++$i)
			{
				$var = $matches[1][$i][0];
				
				if(!isset($this->vars[$var]))
				{
					if(defined($var))
						$html = str_replace('{{'.$var.'}}', constant($var), $html);
					else
						$html = str_replace('{{'.$var.'}}', '', $html);
				}
				else
				{
					$html = str_replace('{{'.$var.'}}', $this->vars[$var], $html);
				}
			}
		}
	}
	
	// ##############################################################################
	// Parsage et affichage des includes html
	// ##############################################################################
	private function ParseInclude(string &$html)
	{
		$pattern = '/\{\{INCLUDE:(.*)\}\}/U';
		preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		if(isset($matches[1]))
		{	
			$file 	= $matches[1][0];

			$retour = file_get_contents(dirname(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')).'/App/Views/'.$file);
			$html 	= preg_replace('/\{\{INCLUDE:(.*)\}\}/U', $retour, $html);
			
			$this->ParsePhp($html);
		}
	}
	
	// ##############################################################################
	// Definition d'une fonction callback 
	// -> Appeller après les affichages par la fonction "SendCallback"
	// ##############################################################################
	protected function callback(string $name)
	{
		$name = str_replace('$', 	'', $name);
		$name = str_replace('()', 	'',	$name);
		
		$this->callback = explode('->', $name);
		
	}
	
	// ##############################################################################
	// Appel de la fonction callback si cette dernière existe
	// ##############################################################################
	protected function SendCallback()
	{
		$classname 		= $this->callback[1];
		$functionname 	= $this->callback[2];
		
		if(is_array($this->callback))
			call_user_func_array(array(${$this->callback[0]}->$classname, $functionname), array());
	}
	
	// ##############################################################################
	// Vérification de l'existence d'un formulaire envoyé
	// ##############################################################################
	public function FormExist() : bool
	{
		return (strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING)) == 'POST') ? true : false;
	}
	
	// ##############################################################################
	// Parsage de l'intégralité des fonctions php
	// ##############################################################################
	protected function ParsePhp(string &$html)
	{
		$this->ParseForEachGlobal($html);
		$this->ParseConditionWithElse($html);
		$this->ParseClassFunction($html);
		$this->ParseFunction($html);
		$this->ParseInclude($html);
		$this->ParseVariable($html);	
	}
	
	
}
?>