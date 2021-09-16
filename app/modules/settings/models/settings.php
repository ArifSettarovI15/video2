<?php
class SettingsClass
{
    /**
     * @var MainClass;
     */
    var $registry;
    /**
     * @var DatabaseClass
     */
    var $db;

    function __construct (&$registry){
        $this->registry=&$registry;
        $this->db=&$this->registry->db;
		$this->initGlobal();
    }
    function GetGroupSettings ($filter_options, $count=10, $start_page=0) {
        $array=array();
        $result=$this->GetGroupSettingsFromDb($filter_options, $count, $start_page);
        while ($result_item = $this->db->fetch_array($result))
        {
            $array[]=$result_item;
        }

        return $array;
    }
    function GetGroupSettingsFromDb ($filter_options, $count, $start_page) {
        $sql=$this->PrepareGroupSettingsWhere($filter_options);
        if ($count=='all') {
            $sql_limit="";
        }
        else {
            $sql_limit="LIMIT ".$start_page.",".$count;
        }

        return $this->db->query_read("SELECT *
        FROM `core_settings_groups`
        ".$sql." ".$sql_limit);
    }

    function PrepareGroupSettingsWhere ($filter_options=array()) {
        $sql='';
        if ($filter_options['title']!='') {
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= "`core_settings_groups`.`cs_group_name` LIKE '%" . $this->db->escape_string_like($filter_options['title']) . "%' ";
        }
        if (intval($filter_options['id']) >0){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_groups`.`cs_group_id`=".$this->db->sql_prepare($filter_options['id']);
        }
        if (is_string($filter_options['status']) or array_key_exists ('status', $filter_options)==false) {
            $filter_options['status']=1;
        }
        if (is_numeric($filter_options['status']) && $filter_options['status']!=-1 ){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_groups`.`cs_group_status`=".$this->db->sql_prepare($filter_options['status']);
        }

        if ($filter_options['order_way']=='desc'){
            $filter_options['order_sort_sql']='DESC';
        }
        else {
            $filter_options['order_sort_sql']='ASC';
        }

        if ($filter_options['order']=='name'){
            $filter_options['order_sql']=' `core_settings_groups`.`cs_group_name`';
        }
        else {
            $filter_options['order_sql']=' `core_settings_groups`.`cs_group_name`';
        }
        if ($filter_options['show_order']) {
            $sql .= " ORDER BY " . $filter_options['order_sql'] . " " . $filter_options['order_sort_sql'];
        }

        return $sql;
    }

    function GetGroupSettingsTotal ($filter_options) {
        $result=$this->GetGroupSettingsTotalFromDb($filter_options);
        return $this->db->num_rows($result);
    }
    function GetGroupSettingsTotalFromDb ($filter_options) {
        $sql=$this->PrepareGroupSettingsWhere($filter_options);
        return $this->db->query_read("SELECT `core_settings_groups`.`cs_group_id`
        FROM `core_settings_groups`
        ".$sql);
    }

    function GetGroupSettingsByTitle ($title) {
        return $this->db->query_first("SELECT *
        FROM `core_settings_groups`
        WHERE `cs_group_name`=".$this->db->sql_prepare($title));
    }
    function GetGroupSettingsByKey ($title) {
        return $this->db->query_first("SELECT *
        FROM `core_settings_groups`
        WHERE `cs_group_key`=".$this->db->sql_prepare($title));
    }
    function AddGroupSettings ($title,$key,$status) {
        $this->db->query_write("INSERT INTO `core_settings_groups`
        (`cs_group_status`,`cs_group_name`,`cs_group_key`)
        VALUES (
        ".$this->db->sql_prepare($status).",
        ".$this->db->sql_prepare($title).",
        ".$this->db->sql_prepare($key)."
        )");
        return $this->db->insert_id();
    }
    function UpdateGroupSettings ($id,$title,$key,$status) {
        return $this->db->query_write("UPDATE `core_settings_groups`
         SET `cs_group_status`=".$this->db->sql_prepare($status).",
         `cs_group_name`=".$this->db->sql_prepare($title).",
         `cs_group_key`=".$this->db->sql_prepare($key)."
         WHERE `cs_group_id`=".$this->db->sql_prepare($id));
    }
	function UpdateGroupBadge ($type,$id,$value) {
		return $this->db->query_write("UPDATE `core_settings_groups`
        SET
        `cs_group_".$type."`= ".$this->db->sql_prepare($value)."
        WHERE cs_group_id=".$this->db->sql_prepare($id));
	}


    function GetGroupSetting ($id) {
        $result_item=$this->GetGroupSettingFromDb($id);
        return $result_item;
    }
    function GetGroupSettingFromDb ($id) {
        return $this->db->query_first("SELECT *
        FROM `core_settings_groups`
        WHERE `core_settings_groups`.`cs_group_id`=".$this->db->sql_prepare($id));
    }


    function DeleteGroupSetting ($id) {
        return $this->db->query_write("DELETE FROM `core_settings_groups`
        WHERE `cs_group_id`=".$this->db->sql_prepare($id));
    }
    function GetSettingsFields ($filter_options, $count=10, $start_page=0) {
        $array=array();
        $result=$this->GetSettingsFieldsFromDb($filter_options, $count, $start_page);
        while ($result_item = $this->db->fetch_array($result))
        {
            $array[$result_item['cs_key']]=$result_item;
        }

        return $array;
    }
    function GetSettingsFieldsFromDb ($filter_options, $count, $start_page) {
        $sql=$this->PrepareGetSettingsFieldsWhere($filter_options);
        if ($count=='all') {
            $sql_limit="";
        }
        else {
            $sql_limit="LIMIT ".$start_page.",".$count;
        }

        return $this->db->query_read("SELECT *
        FROM `core_settings_fields`
        LEFT JOIN core_settings_groups ON `core_settings_fields`.`cs_group_id`=core_settings_groups.cs_group_id
        ".$sql." ".$sql_limit);
    }

    function PrepareGetSettingsFieldsWhere ($filter_options=array()) {
        $sql='';
        if ($filter_options['title']!='') {
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= "`core_settings_fields`.`cs_title` LIKE '%" . $this->db->escape_string_like($filter_options['title']) . "%' ";
        }
        if ($filter_options['group_key']!=''){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_groups`.`cs_group_key`=".$this->db->sql_prepare($filter_options['group_key']);
        }
        if ($filter_options['type']!=''){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_fields`.`cs_type`=".$this->db->sql_prepare($filter_options['type']);
        }

        if (intval($filter_options['group_id']) >0){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_fields`.`cs_group_id`=".$this->db->sql_prepare($filter_options['group_id']);
        }
        if (intval($filter_options['parent_id']) >0){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_fields`.`cs_parent_id`=".$this->db->sql_prepare($filter_options['parent_id']);
        }

        if (is_string($filter_options['status']) or array_key_exists ('status', $filter_options)==false) {
            $filter_options['status']=1;
        }
        if (is_numeric($filter_options['status']) && $filter_options['status']!=-1 ){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_settings_fields`.`cs_status`=".$this->db->sql_prepare($filter_options['status']);
        }

        if ($filter_options['order_way']=='desc'){
            $filter_options['order_sort_sql']='DESC';
        }
        else {
            $filter_options['order_sort_sql']='ASC';
        }

        if ($filter_options['order']=='name'){
            $filter_options['order_sql']=' `core_settings_fields`.`cs_title`';
        }
        elseif ($filter_options['order']=='sort'){
            $filter_options['order_sql']=' `core_settings_fields`.`cs_sort`';
        }
        else {
            $filter_options['order_sql']=' `core_settings_fields`.`cs_title`';
        }
        if ($filter_options['show_order']) {
            $sql .= " ORDER BY " . $filter_options['order_sql'] . " " . $filter_options['order_sort_sql'];
        }

        return $sql;
    }

    function GetSettingsFieldsTotal ($filter_options) {
        $result=$this->GetSettingsFieldsTotalFromDb($filter_options);
        return $this->db->num_rows($result);
    }
    function GetSettingsFieldsTotalFromDb ($filter_options) {
        $sql=$this->PrepareGroupSettingsWhere($filter_options);
        return $this->db->query_read("SELECT `core_settings_fields`.`cs_id`
        FROM `core_settings_fields`
        ".$sql);
    }


    ////
    function GetSettingsByTitle ($group_id,$title,$parent_id=0) {
        return $this->db->query_first("SELECT *
        FROM `core_settings_fields`
        WHERE `cs_group_id`=".$this->db->sql_prepare($group_id)." AND `cs_title`=".$this->db->sql_prepare($title)." and cs_parent_id=".$this->db->sql_prepare($parent_id));
    }
    function GetSettingsByKey ($key) {
        return $this->db->query_first("SELECT *
        FROM `core_settings_fields`
        WHERE  `cs_key`=".$this->db->sql_prepare($key));
    }
	function GetSettingsByKey2 ($group_id,$key) {
		return $this->db->query_first("SELECT *
        FROM `core_settings_fields`
        WHERE   `cs_group_id`=".$this->db->sql_prepare($group_id)." AND `cs_key`=".$this->db->sql_prepare($key));
	}
    function SetSettingValueHook ($field_data,$values,$line_key) {
        return false;
    }


    function GetSettingsFieldTypes () {
        $array=array(
            'text_input'=>array(
                'title'=>'Текстовое поле'
            ),
            'text_area'=>array(
                'title'=>'Область текста'
            ),
            'text_rich'=>array(
	            'title'=>'Визуальный редактор'
            ),
            'repeater'=>array(
                'title'=>'Повторитель'
            ),
            'image'=>array(
                'title'=>'Изображение'
            ),
            'hidden'=>array(
                'title'=>'Скрытое поле'
            ),
            'checkbox'=>array(
	            'title'=>'Чекбокс'
            )
        );
        return $array;
    }
    function AddSettings ($group_id,$title,$key,$status,$caption,$type,$required,$visible,$level,$parent_id) {
        $this->db->query_write("INSERT INTO `core_settings_fields`
        (`cs_group_id`,`cs_status`,`cs_title`,`cs_key`,`cs_caption`,`cs_type`,`cs_required`,`cs_visible`,`cs_level`,`cs_parent_id`)
        VALUES (
        ".$this->db->sql_prepare($group_id).",
        ".$this->db->sql_prepare($status).",
        ".$this->db->sql_prepare($title).",
        ".$this->db->sql_prepare($key).",
        ".$this->db->sql_prepare($caption).",
        ".$this->db->sql_prepare($type).",
        ".$this->db->sql_prepare($required).",
        ".$this->db->sql_prepare($visible).",
        ".$this->db->sql_prepare($level).",
        ".$this->db->sql_prepare($parent_id)."
        )");
        return $this->db->insert_id();
    }
    function UpdateSettings ($id,$group_id,$title,$key,$status,$caption,$type,$required,$visible,$level,$parent_id) {
        return $this->db->query_write("UPDATE `core_settings_fields`
         SET
          `cs_group_id`=".$this->db->sql_prepare($group_id).",
         `cs_status`=".$this->db->sql_prepare($status).",
         `cs_title`=".$this->db->sql_prepare($title).",
         `cs_key`=".$this->db->sql_prepare($key).",
         `cs_caption`=".$this->db->sql_prepare($caption).",
         `cs_type`=".$this->db->sql_prepare($type).",
         `cs_required`=".$this->db->sql_prepare($required).",
         `cs_visible`=".$this->db->sql_prepare($visible).",
         `cs_level`=".$this->db->sql_prepare($level).",
         `cs_parent_id`=".$this->db->sql_prepare($parent_id)."
         WHERE `cs_id`=".$this->db->sql_prepare($id));
    }

    function GetSetting ($id) {
        $result_item=$this->GetSettingFromDb($id);
        return $result_item;
    }
    function GetSettingFromDb ($id) {
        return $this->db->query_first("SELECT *
        FROM `core_settings_fields`
        LEFT JOIN core_settings_groups ON `core_settings_fields`.cs_group_id=core_settings_groups.cs_group_id
        WHERE `core_settings_fields`.`cs_id`=".$this->db->sql_prepare($id));
    }


    function MakeFieldsTree ($current_id=0,$array_data) {
        $i=1;
        $array=array();
        foreach ($array_data as $result_item)
        {
            if ($result_item['cat_id']==$current_id) {
                $result_item['active']=1;
                /*if ($result_item['level']==2) {
                    $array['levels'][$result_item['level']-1][0][$result_item['cat_parent_id']]['active']=1;
                }*/
            }
            $array['data'][$result_item['cs_id']]=$result_item;
            $array['levels'][$result_item['cs_level']][$result_item['cs_parent_id']][$result_item['cs_id']]=$result_item;
            $i++;
        }
        return $array;
    }

    function MakeFieldsTreeUl ($Data,$current_parent='',$level=1,$parent=0,$max_level=999) {
        $list='';
        if ($level>1) {
            $list = '
<ol>
';
        }
        if ($Data['levels'][$level]) {
            if ($Data['levels'][$level][$parent]) {
                foreach ($Data['levels'][$level][$parent] as $c_data) {
                    $sep = '';
                    for ($i = 1; $i <= $level; $i++) {
                        $sep .= '';
                    }

                    $title = $c_data['cs_title'];

                    $list .= '<li data-id="' . $c_data['cs_id'] . '" data-level="' . $c_data['cs_level'] . '" id="sort_id_' . $c_data['cs_id'] . '">
                <div>
                     <div class="toggle">
                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                     </div>
                    <div class="handle">
                        <i class="fa fa-arrows" aria-hidden="true"></i>
                    </div>
                
                    <div>' . $title . ' ('.$c_data['cs_key'].')</div>
                    <div class="bb">
                        <div>
                        <a href="'.BASE_URL.'/manager/settings/'.$Data['group_info']['cs_group_id'].'/fields/edit/' . $c_data['cs_id'] . '/" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                        </div>
                        <div data-value="' . $c_data['cs_id'] . '" class="btn btn-danger btn-xs delete"><i class="fa"></i></div>
                    </div>
                   </div>
            ';
                    if ($Data['levels'][$level + 1][$c_data['cs_id']] && $max_level >= ($level + 1)) {
                        $list .= $this->MakeFieldsTreeUl($Data, $current_parent, $level + 1, $c_data['cs_id'], $max_level);
                    }
                    $list .= '</li>
';
                }
            }
        }
        if ($level>1) {
            $list .= '</ol>';
        }
        return $list;
    }
    function MakeFieldsTreeInputs ($Data,$current_parent='',$level=1,$parent=0,$max_level=999) {
        $list='';
        if ($Data['levels'][$level]) {
            if ($Data['levels'][$level][$parent]) {
                foreach ($Data['levels'][$level][$parent] as $c_data) {
                    $fields=array();
                    if ($c_data['cs_type']=='repeater') {
                        $fields = $Data['levels'][$level + 1][$c_data['cs_id']];
                    }

                        $list .= $this->registry->template->Render('settings/manager/field_types/' . $c_data['cs_type'] . '.html.twig',
                            array(
                                'input_info' => $c_data,
                                'values' => $Data['values'],
                                'fields'=>$fields
                            ));

                }
            }
        }
        return $list;
    }

    function MakeFieldsTreeSelect ($Data,$current_id=0,$level=1,$parent=0,$max_level=999) {
        $list='';
        if ($Data['levels'][$level]) {
            foreach ($Data['levels'][$level][$parent] as $cc) {
                $sep = '';
                for ($i = 1; $i <= $level; $i++) {
                    $sep .= '&nbsp;&nbsp;';
                }
                if ($current_id == $cc['cs_id']) {
                    $selected = ' selected="selected" ';
                } else {
                    $selected = '';
                }
                $list .= '<option ' . $selected . ' value="' . $cc['cs_id'] . '">' . $sep . $cc['cs_title'] . '</option>';
                if ($Data['levels'][$level + 1][$cc['cs_id']] && $max_level >= ($level + 1)) {
                    $list .= $this->MakeFieldsTreeSelect($Data, $current_id, $level + 1, $cc['cs_id'], $max_level);
                }

            }
        }
        return $list;
    }


    function DeleteSetting ($id) {
        return $this->db->query_write("DELETE FROM `core_settings_fields`
        WHERE `cs_id`=".$this->db->sql_prepare($id));
    }

    function UpdateSettingsDbTree ($id,$parent,$sort,$level) {
        $this->db->query_write("UPDATE `core_settings_fields`
        SET
        `cs_parent_id`=".$this->db->sql_prepare($parent).",
        `cs_sort`=".$this->db->sql_prepare($sort).",
        `cs_level`=".$this->db->sql_prepare($level)."
        WHERE `cs_id`=".$this->db->sql_prepare($id));
    }
    function UpdateSettingsSort ($line_key,$sort) {
        $this->db->query_write("UPDATE `core_settings_values`
        SET  `cs_sort`=".$this->db->sql_prepare($sort)."
        WHERE `cs_line`=".$this->db->sql_prepare($line_key));
    }
    function ProcessSettingsSort ($array, $level, $position, $parent=0) {
        foreach ($array as $data) {
            $position++;

            $this->UpdateSettingsDbTree($data['id'], $parent, $position, $level);
            if ($data['children']) {
                $position=$this->ProcessSettingsSort($data['children'], $level + 1, $position, $data['id']);
            }
        }
        return $position;
    }

    function GetFieldValue  ($key) {
        $options=array();
        $options['show_order']=true;
        $options['order']='sort';
    }

    function GetSettingsValue ($id,$line='') {
        return $this->db->query_first("SELECT *
        FROM `core_settings_values`
        WHERE  `cs_line`=".$this->db->sql_prepare($line)."  AND `cs_id`=".$this->db->sql_prepare($id));
    }


    function AddSettingsValue ($cs_id,$csv_value,$cs_line='',$csv_status=1) {
        $this->db->query_write("INSERT INTO `core_settings_values`
        (`cs_id`,`csv_value`,`csv_status`,`cs_line`)
        VALUES (
        ".$this->db->sql_prepare($cs_id).",
        ".$this->db->sql_prepare($csv_value).",
        ".$this->db->sql_prepare($csv_status).",
        ".$this->db->sql_prepare($cs_line)."
        )");
        return $this->db->insert_id();
    }

    function UpdateSettingsValue ($cs_id,$csv_value,$cs_line='',$csv_status=1) {
        return $this->db->query_write("Update `core_settings_values`
        SET `csv_value`=".$this->db->sql_prepare($csv_value).",
        `csv_status`=".$this->db->sql_prepare($csv_status)."
         WHERE `cs_line`=".$this->db->sql_prepare($cs_line)." AND cs_id=".$this->db->sql_prepare($cs_id));
    }

    function DeleteSettingsValue ($cs_id,$line_key='') {
        return $this->db->query_write("DELETE FROM `core_settings_values`
         WHERE `cs_id`=".$this->db->sql_prepare($cs_id)." AND cs_line=".$this->db->sql_prepare($line_key));
    }
    function DeleteSettingsValueByLine ($line_key='') {
        return $this->db->query_write("DELETE FROM `core_settings_values`
         WHERE cs_line=".$this->db->sql_prepare($line_key));
    }
    function GetGroupValues ($filter_s,$full=0,$one_result=0) {
        $array=array();
        $filter_images=array();
        $filter_images['file_module']='settings';
        $filter_images['file_folder']=$filter_s['key'];
        $images=$this->registry->files->GetFiles($filter_images,'all');
        $result=$this->GetGroupValuesFromDb($filter_s);
        while ($result_item = $this->db->fetch_array($result))
        {

            if ($full==0) {
                if ($result_item['cs_type']=='image') {
                    $v = $images[$result_item['csv_value']];
                }
                else {
                    $v = $result_item['csv_value'];
                }
            }
            else {
                $v= $result_item;
                if ($result_item['cs_type']=='image') {
                    $v['csv_value'] = $images[$result_item['csv_value']];
                }
            }
            if ($result_item['cs_line']!='' && $one_result==0) {
                $array[$result_item['parent_key']][$result_item['cs_line']][$result_item['cs_key']] = $v;
            }
            else {
                $array[$result_item['cs_key']] = $v;
            }
        }
        return $array;
    }

    function GetGroupValuesFromDb ($filter_s) {
        $sql=$this->GetGroupValuesWhere($filter_s);
        $query="SELECT  `core_settings_values`.*,CSF.*,`core_settings_groups`.*,CSF2.cs_key as `parent_key`
        FROM `core_settings_values`
        LEFT JOIN  `core_settings_fields` CSF ON  `core_settings_values`.cs_id=CSF.cs_id
        LEFT JOIN  `core_settings_groups` ON  CSF.cs_group_id=`core_settings_groups`.cs_group_id
        LEFT JOIN  `core_settings_fields` CSF2 ON  CSF.cs_parent_id=CSF2.cs_id
        ".$sql;

        return $this->db->query_read($query);
    }

    function GetGroupValuesWhere ($filter_options=array()) {
        $sql='';

	    if (is_numeric($filter_options['status']) && $filter_options['status']!=-1 ){

		    if ($sql != '') {
			    $sql .= ' AND ';
		    } else {
			    $sql .= ' WHERE ';
		    }
		    $sql.="`core_settings_groups`.`cs_group_status`=".$this->db->sql_prepare($filter_options['status']);
	    }
	    elseif($filter_options['status']=='all') {

	    }
	    else {
		    if ($sql != '') {
			    $sql .= ' AND ';
		    } else {
			    $sql .= ' WHERE ';
		    }
		    $sql.="`core_settings_groups`.`cs_group_status`=1";
	    }


        if ($filter_options['key']!=''){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="core_settings_groups.`cs_group_key`=".$this->db->sql_prepare($filter_options['key']);
        }
        if ($filter_options['parent_key']!=''){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="CSF2.cs_key=".$this->db->sql_prepare($filter_options['parent_key']);
        }

        if ($filter_options['line_key']!=''){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="core_settings_values.`cs_line`=".$this->db->sql_prepare($filter_options['line_key']);
        }

        if (intval($filter_options['group_id'])!=0){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="CSF.`cs_group_id`=".$this->db->sql_prepare($filter_options['group_id']);
        }


        if ($filter_options['order_way']=='desc'){
            $filter_options['order_sort_sql']='DESC';
        }
        else {
            $filter_options['order_sort_sql']='ASC';
        }

        if ($filter_options['order']=='sort'){
            $filter_options['order_sql']=' `core_settings_values`.`cs_sort`';

        }
        else {
            $filter_options['order_sql']=' `core_settings_values`.`cs_sort`';
        }
        if ($filter_options['show_order']) {
            $sql .= " ORDER BY " . $filter_options['order_sql'] . " " . $filter_options['order_sort_sql'];
        }

        return $sql;
    }



	public function getCompanyInfo() {
		$filter_s=array();
		$filter_s['key']='company';
		return $this->GetGroupValues($filter_s);
	}

	public function getAdv() {
		$filter_s=array();
		$filter_s['key']='adv';
		$filter_s['show_order']=true;
		 return $this->GetGroupValues($filter_s);
	}

	public function getClients() {
		$filter_s=array();
		$filter_s['key']='clients';
		$filter_s['show_order']=true;
		$aa=$this->GetGroupValues($filter_s);
		return array_slice($aa['clients_list'],0,8);
	}


	public function getComments() {
		$filter_s=array();
		$filter_s['key']='comments';
		$filter_s['show_order']=true;
		$aa=$this->GetGroupValues($filter_s);
		return $aa['comments_list'];
	}

	public function getSteps() {
		$filter_s=array();
		$filter_s['key']='steps';
		$filter_s['show_order']=true;
		return $this->GetGroupValues($filter_s);
	}


	public function initGlobal() {
		$filter_s=array();
		$filter_s['key']='about';
		$this->registry->template->global_vars['fields']['about']=$this->GetGroupValues($filter_s);
	}
}
