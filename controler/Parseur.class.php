<?php
class Parseur
{
	protected $html;
	protected $page;
	protected $template;
	protected $callback;
	public $classes;
	public $vars;
	
	// ##############################################################################
	// Constructeur de classe
	// ##############################################################################
	public function __construct()
	{
		$this->vars = array();
		$this->classes = array();
	}
	
	// ##############################################################################
	// Fonciton de parsage du template
	// ##############################################################################
	public function ParseTemplate($file)
	{
		$this->html = file_get_contents($file);
		$this->ParsePhp($this->html);
		
		return $this->html;
	}
	
	// ##############################################################################
	// Fonciton de parsage d'une page
	// ##############################################################################
	public function ParsePage($file)
	{
		$this->page = file_get_contents($file);
		$this->ParsePhp($this->page);
		
		return $this->page;
	}
	
	// ##############################################################################
	// Parsage d'une boucle et traitement de ses variables
	// ##############################################################################
	private function ParseForEach(&$html)
	{
		$pattern = '/\{\{FOREACH:(.*)->(.*)\(\)\}\}(.*)\{\{\/FOREACH\}\}/s';
		preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		
		if(count($matches))
		{
			$count = count($matches[2]);
			
			for($i = 0; $i < $count; ++$i)
			{
				if(isset($matches[1]) && isset($matches[2]) && isset($matches[3]))
				{		
					$var = $this->{$matches[1][$i][0]};
					$function = $matches[2][$i][0];
					$datas = $matches[3][$i][0];
					$boucle = call_user_func_array(array($var, $function), array());
					
					$retour = '';
					
					foreach($boucle as $data)
					{
						$pattern = '/{(.*)}/U';
						preg_match_all($pattern, $datas, $matches);
						
						$prov = $datas;
						
						foreach($matches[1] as $regex)
						{
							$prov = str_replace('{'.$regex.'}', $data[$regex], $prov);
						}
						
						$retour .= $prov;
					}
					
					$html = preg_replace('/\{\{FOREACH:(.*)->(.*)\(\)\}\}(.*)\{\{\/FOREACH\}\}/s', $retour, $html);
				}
			}
		}
	}

	// ##############################################################################
	// Appel de la fonction callback si cette dernière existe
	// ##############################################################################
	protected function ParseConditionWithElse(&$html)
	{
		$pattern = '/({{IF:(.*)}})(.*)({{ELSE}})(.*)({{ENDIF}})/smU';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		$count = count($matches[2]);
		
		for($i = 0; $i < $count; ++$i)
		{
			if(count($matches) >= 7)
			{
				$condition = $matches[2][$i][0];
				$if = $matches[3][$i][0];
				$else = $matches[5][$i][0];
				
				$conditioncut = explode('->', $condition);
						
				if(count($conditioncut) < 2)
					throw new Exception('La syntaxe de la condition IF est incorrect');
				
				if(!isset($this->classes[$conditioncut[0]]))
					throw new Exception('La classe "'.$conditioncut[0].'" renseignée dans la condition if n\'est pas initialisé dans le parseur');
				
				$conditioncut[1] = str_replace('()', '', $conditioncut[1]);
				$retour = call_user_func_array(array($this->classes[$conditioncut[0]], $conditioncut[1]), array());

				$pattern = '{{IF:'.$conditioncut[0].'->'.$conditioncut[1].'()}}'.$if.'{{ELSE}}'.$else.'{{ENDIF}}';
				$html = str_replace($pattern, ($retour) ? $if : $else, $html);
			}
		}
	}
	
	// ##############################################################################
	// Parsage de l'appel d'une fonction d'une classe
	// ##############################################################################
	private function ParseClassFunction(&$html)
	{
		$pattern = '/\{\{(.*)->(.*)\(\)\}\}/U';
		preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		
		if(isset($matches[1]) && isset($matches[2]))
		{	
			$count = count($matches[2]);
			for($i = 0; $i < $count; ++$i)
			{
				$class = $matches[1][$i][0];
				$function = $matches[2][$i][0];
							
				$retour = call_user_func_array(array($this->classes[$class], $function), array());
				
				$html = str_replace('{{'.$class.'->'.$function.'()}}', $retour, $html);
			}
		}
	}
	
	// ##############################################################################
	// Parsage d'une fonction procédurale
	// ##############################################################################
	private function ParseFunction(&$html)
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
				
				$html = str_replace('{{'.$function.'()}}', $retour, $html);
			}
		}
	}
	
	// ##############################################################################
	// Parsage et affichages des variables
	// ##############################################################################
	private function ParseVariable(&$html)
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
					throw new Exception('La variable {'.$var.'} n\'existe pas dans le parseur');
				
				$html = str_replace('{{'.$var.'}}', $this->vars[$var], $html);
			}
		}
	}
	
	// ##############################################################################
	// Parsage et affichage des includes html
	// ##############################################################################
	private function ParseInclude(&$html)
	{
		$pattern = '/\{\{INCLUDE=(.*)\}\}/U';
		preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE, 3);
		if(isset($matches[1]))
		{	
			$file = $matches[1][0];
			$retour = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/Datas/Template/'.$this->template.'/Pages/'.$file);
			
			$html = preg_replace('/\{\{INCLUDE=(.*)\}\}/s', $retour, $html);
			
			$this->ParsePhp();
		}
	}
	
	// ##############################################################################
	// Definition d'une fonction callback 
	// -> Appeller après les affichages par la fonction "SendCallback"
	// ##############################################################################
	protected function callback($name)
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
		$classname = $this->callback[1];
		$functionname = $this->callback[2];
		
		if(is_array($this->callback))
			call_user_func_array(array(${$this->callback[0]}->$classname, $functionname), array());
	}
	
	// ##############################################################################
	// Parsage de l'intégralité des fonctions php
	// ##############################################################################
	protected function ParsePhp(&$html)
	{
		$this->ParseForEach($html);
		$this->ParseConditionWithElse($html);
		$this->ParseClassFunction($html);
		$this->ParseFunction($html);
		$this->ParseInclude($html);
		$this->ParseVariable($html);	
	}
	
	
}
?>