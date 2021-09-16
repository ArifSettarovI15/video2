<?php
class SpravClass
{
	/**
	 * @var MainClass;
	 */
	var $registry;
	/**
	 * @var DatabaseClass
	 */
	var $db;

	var $list;

	function SpravClass (&$registry){
		$this->registry=&$registry;
		$this->db=&$this->registry->db;
		$this->list=array();
		$sprav=$this->GetSprav(
			array(),'all'
		);
		foreach ($sprav as $s) {
			$this->list[$s['sprav_name']]=$s;
		}
	}


	public function GetSprav ($filter_options=array(), $count=20, $start_page=0) {
		$array=array();
		$result=$this->GetSpravFromDb($filter_options,$count,$start_page);
		while ($result_item = $this->db->fetch_array($result))
		{
			$result_item=$this->PrepareSpravData($result_item);
			$array[$result_item['sprav_id']]=$result_item;
		}

		return $array;
	}

	private function GetSpravFromDb ($filter_options=array(), $count=20, $start_page=0){
		$sql=$this->PrepareSpravWhere($filter_options);
		if ($count=='all') {
			$sql_limit="";
		}
		else {
			$sql_limit="LIMIT ".$start_page.",".$count;
		}

		return $this->db->query_read("SELECT `core_sprav`.*
        FROM `core_sprav`
        ".$sql." ".$sql_limit);
	}

	private function PrepareSpravData ($result_item,$full=0) {
		if ($result_item['sprav_label']=='') {
			$result_item['sprav_label']=$result_item['sprav_title'];
		}
		return $result_item;
	}

	private function PrepareSpravWhere ($filter_options=array()){
		$sql='';

		if (is_numeric($filter_options['sprav_filter']) && $filter_options['sprav_filter']!=-1 ){

			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_filter`=".$this->db->sql_prepare($filter_options['sprav_filter']);
		}

		if (is_numeric($filter_options['sprav_status']) && $filter_options['sprav_status']!=-1 ){

			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_status`=".$this->db->sql_prepare($filter_options['sprav_status']);
		}
		if ($filter_options['sprav_title_like']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.='`core_sprav`.`sprav_title` LIKE "%'.$this->db->escape_string_like($filter_options['sprav_title_like']).'%"';
		}
		if ($filter_options['sprav_title']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_title`=".$this->db->sql_prepare($filter_options['sprav_title']);
		}
		if ($filter_options['sprav_code']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_code`=".$this->db->sql_prepare($filter_options['sprav_code']);
		}
		if ($filter_options['sprav_name']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_name`=".$this->db->sql_prepare($filter_options['sprav_name']);
		}
		if (intval($filter_options['sprav_id'])!=0){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_id`=".$this->db->sql_prepare($filter_options['sprav_id']);
		}

		if ($filter_options['order']=='title'){
			$filter_options['order_sql']=' `core_sprav`.`sprav_title` ASC';
		}
		elseif ($filter_options['order']=='sort'){
			$filter_options['order_sql']=' `core_sprav`.`sprav_sort` ASC';
		}
		else {
			$filter_options['order_sql']=' `core_sprav`.`sprav_title` ASC';
		}

		if ($filter_options['order_sql'] && $filter_options['show_order']) {
			$sql.=" ORDER BY ".$filter_options['order_sql'];
		}

		return $sql;
	}

	public function GetSpravTotal ($filter_options=array()) {
		$result=$this->GetSpravTotalFromDb($filter_options);
		return intval($result['count']);
	}

	private function GetSpravTotalFromDb($filter_options=array()){
		$sql=$this->PrepareSpravWhere($filter_options);
		return $this->db->query_first("SELECT count(`core_sprav`.`sprav_id`) as count
        FROM `core_sprav`
        ".$sql);
	}


	public function GetSpravItem ($filter_options=array(),$full=0) {
		$result_item=$this->GetSpravItemFromDb($filter_options);
		if ($result_item) {
			$result_item = $this->PrepareSpravData($result_item, $full);
		}
		return $result_item;
	}

	private function GetSpravItemFromDb ($filter_options=array()) {
		$sql=$this->PrepareSpravWhere($filter_options);
		if ($sql) {
			return $this->db->query_first("SELECT `core_sprav`.*
            FROM `core_sprav`
            ".$sql);
		}
		return false;

	}

	public function UpdateSpravItem ($id,$value,$name,$label,$style,$ext, $desc, $type) {
		return $this->db->query_write("UPDATE `core_sprav`
        SET
        `sprav_title`= ".$this->db->sql_prepare($value).",
        `sprav_name`=".$this->db->sql_prepare($name).",
        `sprav_label`=".$this->db->sql_prepare($label).",
        `sprav_style`=".$this->db->sql_prepare($style).",
        `sprav_ext`=".$this->db->sql_prepare($ext).",
        `sprav_desc`=".$this->db->sql_prepare($desc).",
        `sprav_data_type`=".$this->db->sql_prepare($type)."
        WHERE sprav_id=".$this->db->sql_prepare($id));
	}
	public function UpdateSpravCode ($id,$code) {
		return $this->db->query_write("UPDATE `core_sprav`
        SET
        `sprav_code`=".$this->db->sql_prepare($code)."
        WHERE sprav_id=".$this->db->sql_prepare($id));
	}
	public function AddSpravItem($value,$name,$label,$style,$ext, $desc, $type='string', $id=NULL) {

		$dop_id='';
		$dop_id2='';
		if (is_null($id)===false) {
			$dop_id='`sprav_id`,';
			$id=$this->db->sql_prepare($id);
			$dop_id2=$id.',';
		}

		$this->db->query_write("INSERT INTO `core_sprav`
        (".$dop_id."`sprav_title`,`sprav_name`,`sprav_label`,`sprav_style`,`sprav_ext`,`sprav_desc`, sprav_data_type)
        VALUES (
        ".$dop_id2."
        ".$this->db->sql_prepare($value).",
       ".$this->db->sql_prepare($name).",
       ".$this->db->sql_prepare($label).",
       ".$this->db->sql_prepare($style).",
       ".$this->db->sql_prepare($ext).",
       ".$this->db->sql_prepare($desc).",
       ".$this->db->sql_prepare($type)."
        )");
		return $this->db->insert_id();
	}

	public function UpdateSpravItemBadge ($type,$id,$value) {
		return $this->db->query_write("UPDATE `core_sprav`
        SET
        `sprav_".$type."`= ".$this->db->sql_prepare($value)."
        WHERE sprav_id=".$this->db->sql_prepare($id));
	}


	public function DeleteSpravItem ($filter_options=array()){
		$sql=$this->PrepareSpravWhere($filter_options);
		if ($sql!='') {
			return $this->db->query_write("DELETE FROM `core_sprav`
        ".$sql);
		}
		return false;
	}

	public function UpdateSpravSort ($sprav_id, $sort){
		return $this->db->query_write("UPDATE `core_sprav`
        SET sprav_sort=".$this->db->sql_prepare($sort)."
        WHERE sprav_id=".$this->db->sql_prepare($sprav_id));
	}

	public function GetSpravValues ($filter_options=array(), $count='all', $start_page=0) {
		$array=array();
		$result=$this->GetSpravValuesFromDb($filter_options,$count,$start_page);
		while ($result_item = $this->db->fetch_array($result))
		{
			$result_item=$this->PrepareSpravValuesData($result_item);
			if ($filter_options['group_array']=='sprav_id') {
				$array[$result_item['sprav_id']][$result_item['svid']]=$result_item;
			}
			else {
				$array[$result_item['svid']]=$result_item;
			}

		}

		return $array;
	}

	private function GetSpravValuesFromDb ($filter_options=array(), $count=20, $start_page=0){
		$sql=$this->PrepareSpravValuesWhere($filter_options);
		if ($count=='all') {
			$sql_limit="";
		}
		else {
			$sql_limit="LIMIT ".$start_page.",".$count;
		}

		$join='';
		$select_dop='';
		if ($filter_options['hide_join']) {

		}
		else {
			$join.=' LEFT JOIN `core_sprav` ON `core_sprav_values`.`sprav_id`=`core_sprav`.`sprav_id` ';
		}
		if ($filter_options['join_image']) {
			$join.=' LEFT JOIN `core_files`  ON `core_sprav_values`.`svid_icon`=`core_files`.`file_id` ';
			$select_dop=',`core_files`.*';
		}

		return $this->db->query_read("SELECT `core_sprav_values`.* ".$select_dop."
        FROM `core_sprav_values`
        ".$join."
        ".$sql." ".$sql_limit);
	}

	private function PrepareSpravValuesData ($result_item,$full=0) {
		$result_item=$this->registry->files->FilePrepare($result_item);
		if ($result_item['svid_syn_data']==''){
			$result_item['svid_syn_data']=array();
		}
		else {
			$result_item['svid_syn_data']=unserialize($result_item['svid_syn_data']);
		}
		$result_item['sprav_icon_url'] = $this->registry->files->GetImageUrl($result_item,'small');
		$result_item['sprav_image_url'] = $this->registry->files->GetImageUrl($result_item,'normal');
		return $result_item;
	}

	private function PrepareSpravValuesWhere ($filter_options=array()){
		$sql='';

		if ($filter_options['sprav_name']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav`.`sprav_name`=".$this->db->sql_prepare($filter_options['sprav_name']);
		}


		if (isset($filter_options['svid_status'])) {
			if (is_numeric($filter_options['svid_status']) && $filter_options['svid_status']!=-1 ){

				if ($sql != '') {
					$sql .= ' AND ';
				} else {
					$sql .= ' WHERE ';
				}
				$sql.="`core_sprav_values`.`svid_status`=".$this->db->sql_prepare($filter_options['svid_status']);
			}
		}
		else {
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.=" `core_sprav_values`.`svid_status`=1 ";
		}


		if ($filter_options['svid_title_like']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.='`core_sprav_values`.`svid_title` LIKE "%'.$this->db->escape_string_like($filter_options['svid_title_like']).'%"';
		}
		if ($filter_options['svid_title']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav_values`.`svid_title`=".$this->db->sql_prepare($filter_options['svid_title']);
		}
		if (is_array($filter_options['spravs']) and count($filter_options['spravs'])>0){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}

			$in=array();
			foreach ($filter_options['spravs'] as $sprav_id){
				$sprav_id=intval($sprav_id);
				$in[$sprav_id]=$this->db->sql_prepare($sprav_id);
			}
			if (count($in)>0) {
				$sql.="`core_sprav_values`.`sprav_id` IN (".implode(',',$in).')';
			}

		}

		if ($filter_options['svid_eng']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav_values`.`svid_eng`=".$this->db->sql_prepare($filter_options['svid_eng']);
		}


		if (intval($filter_options['svid'])!=0){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav_values`.`svid`=".$this->db->sql_prepare($filter_options['svid']);
		}
		if (intval($filter_options['sprav_id'])!=0){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav_values`.`sprav_id`=".$this->db->sql_prepare($filter_options['sprav_id']);
		}


		if (isset($filter_options['et']) and $filter_options['et']){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.=" (`core_sprav_values`.`svid_title`>=".$this->db->sql_prepare(($filter_options['et']-3))." AND
			`core_sprav_values`.`svid_title`<=".$this->db->sql_prepare(($filter_options['et']+3))."
			)";
		}

		if (isset($filter_options['with_syn']) and $filter_options['with_syn']){

		}
		else {
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_sprav_values`.`svid_syn_id`=0";
		}

		if ($filter_options['order']=='title'){
			$filter_options['order_sql']=' `core_sprav_values`.`svid_title` ASC';
		}
		else {
			$filter_options['order_sql']=' `core_sprav_values`.`svid_title` ASC';
		}

		if ($filter_options['order_sql'] && $filter_options['show_order']) {
			$sql.=" ORDER BY ".$filter_options['order_sql'];
		}

		return $sql;
	}

	public function GetSpravValuesTotal ($filter_options=array()) {
		$result=$this->GetSpravValuesTotalFromDb($filter_options);
		return intval($result['count']);
	}

	private function GetSpravValuesTotalFromDb($filter_options=array()){
		$sql=$this->PrepareSpravValuesWhere($filter_options);
		return $this->db->query_first("SELECT count(`core_sprav_values`.`svid`) as count
        FROM `core_sprav_values`
        ".$sql);
	}

	public function GetSpravValue ($filter_options=array(),$full=0) {
		$result_item=$this->GetSpravValueFromDb($filter_options);
		if ($result_item) {
			$result_item = $this->PrepareSpravValuesData($result_item, $full);
		}
		return $result_item;
	}

	private function GetSpravValueFromDb ($filter_options=array()) {
		$sql=$this->PrepareSpravValuesWhere($filter_options);
		if ($sql) {
			return $this->db->query_first("SELECT `core_sprav_values`.*
            FROM `core_sprav_values`
            ".$sql);
		}
		return false;

	}
	public function UpdateSpravValueSynData ($id,$syn_data) {
		return $this->db->query_write("UPDATE `core_sprav_values`
        SET
        `svid_syn_data`= ".$this->db->sql_prepare($syn_data)."
        WHERE svid=".$this->db->sql_prepare($id));
	}
	public function UpdateSpravValueSyn ($id,$syn_id) {
		return $this->db->query_write("UPDATE `core_sprav_values`
        SET
        `svid_syn_id`= ".$this->db->sql_prepare($syn_id)."
        WHERE svid=".$this->db->sql_prepare($id));
	}
	public function UpdateSpravValue ($id,$sprav_id,$title,$eng,$value,$svg='',$photo_id) {
		return $this->db->query_write("UPDATE `core_sprav_values`
        SET
        `sprav_id`= ".$this->db->sql_prepare($sprav_id).",
        `svid_title`= ".$this->db->sql_prepare($title).",
        `svid_eng`= ".$this->db->sql_prepare($eng).",
        `svid_value`= ".$this->db->sql_prepare($value).",
        `svid_icon`= ".$this->db->sql_prepare($photo_id).",
        `svid_svg`= ".$this->db->sql_prepare($svg)."
        WHERE svid=".$this->db->sql_prepare($id));
	}

	public function AddSpravValue($sprav_id,$title,$eng='',$value='',$svg='',$photo_id=0,$status=1, $id=NULL) {

		$id_key='';
		if (is_null($id)) {

		}
		else {
			$id=$this->db->sql_prepare($id).',';
			$id_key='svid, ';
		}

		$this->db->query_write("INSERT INTO `core_sprav_values`
        (".$id_key."`sprav_id`,`svid_title`,`svid_eng`,`svid_value`,`svid_icon`,`svid_status`,`svid_svg`)
        VALUES (
        ".$id."
        ".$this->db->sql_prepare($sprav_id).",
         ".$this->db->sql_prepare($title).",
         ".$this->db->sql_prepare($eng).",
         ".$this->db->sql_prepare($value).",
         ".$this->db->sql_prepare($photo_id).",
         ".$this->db->sql_prepare($status).",
         ".$this->db->sql_prepare($svg)."
        )");
		return $this->db->insert_id();
	}


	public function UpdateSpravValueBadge ($type,$id,$value) {
		return $this->db->query_write("UPDATE `core_sprav_values`
        SET
        `svid_".$type."`= ".$this->db->sql_prepare($value)."
        WHERE svid=".$this->db->sql_prepare($id));
	}


	public function DeleteSpravValue ($filter_options=array()){
		$sql=$this->PrepareSpravValuesWhere($filter_options);
		if ($sql!='') {
			return $this->db->query_write("DELETE FROM `core_sprav_values`
        ".$sql);
		}
		return false;
	}

	public function getSpravIdValues($sprav_id) {
		$filter_options=array();
		$filter_options['sprav_id']=$sprav_id;
		$filter_options['svid_status']=true;
		$filter_options['join_image']=true;
		$filter_options['show_order']=true;
		return $this->GetSpravValues($filter_options);
	}
}
