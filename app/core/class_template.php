<?php


class TemplateClass
{
	/**
	 * @var MainClass;
	 */
	var $registry;
	/**
	 * @var Twig_Environment;
	 */
	var $twig;

	/**
	 * @var Twig_Environment;
	 */

	var $global_vars=array();

	function __construct (&$registry){
		$this->registry=&$registry;

		$reload=false;
		if (strpos(BASE_URL,'.loc' )) {
			$reload=true;
		}
		$loader = new \Twig\Loader\FilesystemLoader(ROOT_DIR . '/app/views/');
		$this->twig = new \Twig\Environment($loader, array(
			'cache' => ROOT_DIR . '/app/views/cache',
			'auto_reload'=>$reload,
			'debug'=>true
		));
		require_once 'twig_ext.php';
	}

	function SetPageAttributes ($header=array(),$page=array()) {
		$this->global_vars['header']=$header;
		$this->global_vars['page']=$page;
	}


	function PrepareGlobalVars ($local_vars=array()) {
		$user_info=$this->registry->user_info;
		unset($user_info['password']);
		unset($user_info['salt']);
		$this->global_vars=array_merge($this->global_vars,array(
			'base_url'=>BASE_URL,
			'server'=>$_SERVER,
			'file_sizes'=>$this->registry->files->GetImageSizes(),
			'settings'=>$this->registry->settings_values
		));
		if (isset($this->registry->seo)) {
			$this->registry->seo->Init();
		}

		$local_vars['global']=$this->global_vars;
		return $local_vars;
	}
	function DisplayJson ($data=array()){
		echo json_encode($data);
		exit;
	}
	function Display ($data=array()){

		$path=$this->registry->route->mapping['twig'];

		$this->DisplayCore($path,$data);
	}
	function RenderDefault ($data=array()){
		$path=$this->registry->route->mapping['twig'];
		$data=$this->PrepareGlobalVars($data);
		return $this->renderTemplate($path, $data);
	}
	function DisplayCore ($path,$data=array()){
		echo $this->Render($path,$data);
	}
	function Render ($path,$data=array()){
		$data=$this->PrepareGlobalVars($data);
		return $this->renderTemplate($path, $data);
	}

	private function renderTemplate($path, $data) {
		$html='';

		try {
			$html = $this->twig->render( $path, $data );
		} catch ( \Twig\Error\LoaderError $e ) {

		} catch ( \Twig\Error\RuntimeError $e ) {
		} catch ( \Twig\Error\SyntaxError $e ) {
		}
		$html=$this->registry->speed->optiHtml($html);
		return $html;
	}


}
