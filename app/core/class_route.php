<?php

class RouteClass
{
	var $route_url;
	/**
	 * @var MainClass
	 */
	var $registry;
	var $parts=array();
	var $do='';
	var $action='';
	var $mapping;
	var $mapping_array=array();
	var $mapping_array2=array();
	/**
	 * @var DatabaseClass
	 */
	var $db;

	/**
	 * @var MainClass
	 */
	function RouteClass ($registry) {
		$this->registry=&$registry;
		$this->db=&$this->registry->db;
		$this->GetMappingArray2();
	}

	function GetParentRules($id) {
		$rules=array();
		if ($id!=0 and isset($this->mapping_array2[$id])) {
			$result_item=$this->mapping_array2[$id];
			$parent=$this->GetParentRules($result_item['parent_id']);
			$rules=array_merge($result_item['rules'],$parent);
		}
		return $rules;
	}

	function GetMappingArray() {
		$result=$this->GetRoutesFromDb();
		while ($result_item = $this->db->fetch_array($result)) {
			if ($result_item['rules'] != '') {
				$result_item['rules'] = unserialize($result_item['rules']);
			}
			$this->mapping_array2[$result_item['id']]=$result_item;
		}
	}
	function GetMappingArray2() {
		$this->GetMappingArray();
		foreach ($this->mapping_array2 as $result_item ) {
			$parent=$this->GetParentRules($result_item['parent_id']);
			$result_item['rules']=array_merge($result_item['rules'] ,$parent);
			$this->mapping_array[$result_item['id']]=$result_item;
		}
	}
	function GetRoutesFromDb (){
		return $this->db->query_read("SELECT * FROM `core_routes` ORDER BY `sort`");
	}

	function ParseRoute ($route){
		$this->route_url=$route;
		$this->CleanUrl();
		$this->GetMapping();
	}

	function CleanUrl () {
		if (strpos($_SERVER['REQUEST_URI'], '?mfp=') or strpos($_SERVER['REQUEST_URI'], '?route=')
		    or strpos($_SERVER['REQUEST_URI'], '?filter=') or strpos($_SERVER['REQUEST_URI'], '?sort=')) {

			$g=$_SERVER['REQUEST_URI'];
			$g=explode('?mfp=',$g);
			$g=explode('?route=',$g[0]);
			$g=explode('?filter=',$g[0]);
			$g=explode('?sort=',$g[0]);
			SiteRedirect( $g[0],301);
		}
		if (strpos($_SERVER['REQUEST_URI'], 'page/0/')) {
			SiteRedirect( str_replace('page/0/','',$_SERVER['REQUEST_URI']),301);
		}
		$this->DeleteIndexFile();
		$this->CleanSlashes();
		$this->CheckSlashEnd();
	}
	function DeleteIndexFile (){
		$r_url=str_replace('/index.php','',$_SERVER['REQUEST_URI']);
		if ( $r_url!=$_SERVER['REQUEST_URI']) {
			SiteRedirect( $r_url,301);
		}
	}
	function ExcludeSlashExt () {
		$ext = pathinfo($this->route_url, PATHINFO_EXTENSION);

		$check=false;
		$k=explode('?',$_SERVER['REQUEST_URI']);

		if (in_array($ext,array('js','html','htm')) or $k[0]=='/') {
			$check=true;
		}

		return $check;
	}
	function ExcludeSlash () {
		if ($this->ExcludeSlashExt()==false
		    && preg_match_all('#assets#Us',  $_SERVER['REQUEST_URI'], $matches)==false
		    && preg_match_all('#uploads#Us',  $_SERVER['REQUEST_URI'], $matches)==false
		    && $_SERVER['REQUEST_URI']!='/'
		    && substr($this->route_url,strlen($this->route_url)-1,1)!='/'
		) {
			return false;
		}
		else {
			return true;
		}
	}

	function CleanSlashes () {
		if(preg_match_all('#//#Us', $_SERVER['REQUEST_URI'], $matches)) {
			while (preg_match_all('#//#Us', $_SERVER['REQUEST_URI'], $matches)) {
				$_SERVER['REQUEST_URI'] = str_replace('//', '', $_SERVER['REQUEST_URI']);
			}
			SiteRedirect($_SERVER['REQUEST_URI'],301);
		}
	}
	function CheckSlashEnd (){
		if ($this->ExcludeSlash()==false) {
			SiteRedirect($this->route_url.'/',301);
		}
	}

	function  ReplaceSlash ($string) {
		$string=str_replace('-!!1!!-','/',$string);
		return $string;
	}

	function GetMapping () {

		if (isset($this->mapping_array)) {
			foreach ($this->mapping_array as $value) {
				$rule=$value['regexp_value'];
				$rule_reg=$rule;
				if ($rule!='' && $this->ExcludeSlashExt()==false){
					$rule_reg=$rule.'/';
				}

				if (preg_match_all('#^' . $rule_reg . '$#Us',  $this->route_url, $matches)) {

					if ($rule_reg=='' && $this->route_url!='') {
						$this->registry->error->PageNotFound();
					}

					$this->mapping = $value;
					$this->mapping['twig']=$value['twig'];
					if ($this->mapping['rules']!='') {
						foreach ($this->mapping['rules'] as $field) {

							if ($field['type']=='') {
								$field['type']=TYPE_STR;
							}

							if (isset($field['name']) && (isset($this->registry->GPC[$field['name']])==false OR
							                              (isset($this->registry->GPC[$field['name']]) && $this->registry->GPC[$field['name']]=='')) ) {
								if (isset($field['static']) && $field['static']==1) {
									$variable=$this->registry->input->clean($field['value'], TYPE_STR);
								}else {
									$matches[$field['pos']][0]=$this->ReplaceSlash($matches[$field['pos']][0]);
									$variable=$this->registry->input->clean($matches[$field['pos']][0], constant ($field['type']));
								}
								if ($field['name']!='') {
									$this->registry->GPC[$field['name']] = $variable;
								}
							}

						}
					}
					else {
						$this->registry->GPC['do']=$rule;
					}
					if ($this->registry->GPC['do']=='') {
						$this->registry->GPC['do']=$rule;
					}
					$this->do=$this->registry->GPC['do'];
					$this->action=$this->registry->GPC['action'];
					return true;
				}
			}

			$this->registry->error->PageNotFound();
		}
		else {
			$this->registry->error->PageNotFound();
		}

	}

}
