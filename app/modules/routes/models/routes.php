<?php
class RouteAdminClass
{
    /**
     * @var MainClass;
     */
    var $registry;
    /**
     * @var DatabaseClass
     */
    var $db;

    function RouteAdminClass (&$registry){
        $this->registry=&$registry;
        $this->db=&$this->registry->db;

    }


    /**
     * @return bool|mysqli_result|string
     */
    function GetRoutesFromDb (){
        return $this->db->query_read("SELECT *
        FROM `core_routes`
        ORDER BY `sort` ASC, `regexp_value` ASC");
    }


    /**
     * @return array
     */
    function  GetRoutes () {
        $array=array();
        $result=$this->GetRoutesFromDb();
        while ($result_item = $this->db->fetch_array($result))
        {
            $array[]=$result_item;
        }
        $array=$this->MakeRoutesTree($array);
        return $array;
    }


    /**
     * @return array
     */
    function GetModulesList () {
        $array=array();
        $where=ROOT_DIR.'/app/modules/';
        $Open = opendir($where);
        while ($Files = readdir($Open)) {
            $dir = $where . "/" . $Files;

            if ($Files != '.' && $Files != '..') {
                if (is_dir($dir)) {
                    $array[] = $Files;
                }
            }
        }

        closedir($Open);
        return $array;
    }

    /**
     * @param $id int
     * @param $parent int
     * @param $sort int
     * @param $level int
     * @return bool|mysqli_result|string
     */
    function UpdateRoutesSort ($id, $parent, $sort, $level) {
        return $this->db->query_write("UPDATE `core_routes`
        SET
        `parent_id`=".$this->db->sql_prepare($parent).",
        `sort`=".$this->db->sql_prepare($sort).",
        `level`=".$this->db->sql_prepare($level)."
        WHERE `id`=".$this->db->sql_prepare($id));
    }

    /**
     * @param $array array
     * @param $level int
     * @param $position int
     * @param int $parent
     * @return mixed
     */
    function ProcessRoutesSort ($array, $level, $position, $parent=0) {
        foreach ($array as $data) {
            $position++;

            $this->UpdateRoutesSort($data['id'], $parent, $position, $level);
            if ($level>0) {
                $this->ProcessUpdateRouteRegexp($data['id']);
            }
            if ($data['children']) {
                $position=$this->ProcessRoutesSort($data['children'], $level + 1, $position, $data['id']);
            }
        }
        return $position;
    }


    /**
     * @param $array_data array
     * @return array
     */
    function MakeRoutesTree ($array_data) {
        $i=1;
        $array=array();
        foreach ($array_data as $result_item)
        {
            $array['data'][$result_item['id']]=$result_item;
            $array['levels'][$result_item['level']][$result_item['parent_id']][$result_item['id']]=$result_item;
            $i++;
        }
        return $array;
    }

    /**
     * @param $id int
     * @return mixed
     */
    function GetRouteInfoFromDb ($id){
        return $this->db->query_first("SELECT `core_routes`.*
        FROM `core_routes`
        WHERE `core_routes`.`id`=".$this->db->sql_prepare($id));
    }

    /**
     * @param $id int
     * @return mixed
     */
    function GetRouteInfo ($id)
    {
        $data=$this->GetRouteInfoFromDb($id);
        if ($data) {
            $data['rules']=unserialize($data['rules']);
            $data['parts']=explode('/',$data['regexp_value']);
            return $data;
        }
        else {
            return false;
        }
    }

    /**
     * @param $id int
     * @return bool|mysqli_result|string
     */
    function DeleteRoute ($id) {
        return $this->db->query_write("DELETE FROM `core_routes`
        WHERE `id`=".$this->db->sql_prepare($id));
    }


    /**
     * @param $list array
     * @param $levels array
     * @param $current_level int
     * @param $id int
     * @return array
     */
    function GetChildrenRoutes_lookup ($list, $levels, $current_level, $id) {
        if ($levels[$current_level+1][$id] and ($max_level==false OR $max_level>=$current_level+1)) {
            foreach ($levels[$current_level+1][$id] as $element) {
                $list[]=$element;
                $list=$this->GetChildrenRoutes_lookup($list,$levels, $current_level + 1,$element['id']);
            }

        }
        return $list;
    }

    function GetChildrenRoutes ($id)
    {
        $routes_list = $this->GetRoutes();
        $list=array();
        $list=$this->GetChildrenRoutes_lookup($list,$routes_list['levels'],$routes_list['data'][$id]['level'],$id);
        return $list;
    }


    /**
     * @param $module string
     * @return array
     */
    function GetModulesTemplatesList ($module) {
        $where=ROOT_DIR.'/app/views/'.$module;
        $array=GetDirListRecursive($where,$where);
        foreach ($array as $key=>$a) {
            $array[$key]=$module.$a;

        }
        return $array;
    }

    /**
     * @param $module string
     * @return array
     */
    function GetModulesActionsList ($module) {
        $where=ROOT_DIR.'/app/modules/'.$module.'/actions';
        $array=GetDirListRecursive($where,$where);
        return $array;
    }


    /**
     * @param $value string
     * @return mixed
     */
    function GetRoutesByRegexp ($value){
        $data=$this->GetRoutesByRegexpFromDb($value);
        return $data;
    }

    /**
     * @param $value string
     * @return mixed
     */
    function GetRoutesByRegexpFromDb ($value){
        return $this->db->query_first("SELECT *
        FROM `core_routes`
        WHERE `regexp_value`=".$this->db->sql_prepare($value));
    }

    /**
     * @param $rules array
     * @return array
     */
    function PrepareRules($rules) {
        $new_rules=array();
        $dyn_pos=1;
        if (is_array($rules)) {
            foreach ($rules as $r) {
                if ($r['static']==1) {
                    $new_rules[]=$r;
                }
                elseif ($r['static']==0) {
                    $r['pos']=$dyn_pos;
                    $new_rules[]=$r;
                    $dyn_pos++;
                }
            }
        }
        return $new_rules;
    }


    /**
     * @param $regexp_value string
     * @param $value string
     * @param $module string
     * @param $action string
     * @param $rules array
     * @param $twig string
     * @param $parent int
     * @param $level int
     * @return mixed
     */
    function InsertRoute($regexp_value, $value, $module, $action, $rules, $twig, $parent,$level){
        $this->db->query_write("INSERT INTO `core_routes`
        (`regexp_value`,`value`, `module`,`action`, `rules`,`twig`,`parent_id`,`level`)
        VALUES (
        ".$this->db->sql_prepare($regexp_value).",
        ".$this->db->sql_prepare($value).",
        ".$this->db->sql_prepare($module).",
        ".$this->db->sql_prepare($action).",
        ".$this->db->sql_prepare($rules).",
        ".$this->db->sql_prepare($twig).",
        ".$this->db->sql_prepare($parent).",
        ".$this->db->sql_prepare($level)."
        )");
        return $this->db->insert_id();
    }

    /**
     * @param $regexp_value string
     * @param $value string
     * @param $module string
     * @param $action string
     * @param $rules array
     * @param $twig string
     * @param $parent int
     * @param $level int
     * @return mixed
     */
    function AddRoute ($regexp_value, $value,$module, $action, $rules, $twig, $parent, $level) {
        //$rules=$this->PrepareRules($rules);
        $rules=serialize($rules);
        $id=$this->InsertRoute($regexp_value,$value, $module,$action,$rules,$twig,$parent,$level);
        return $id;
    }


    /**
     * @param $id int
     * @param $regexp_value string
     * @param $value string
     * @param $module string
     * @param $action string
     * @param $rules array
     * @param $twig string
     * @param $parent int
     * @param $level int
     * @return bool|mysqli_result|string
     */
    function UpdateRoute($id, $regexp_value, $value, $module, $action, $rules, $twig, $parent, $level){
       // $rules=$this->PrepareRules($rules);
        $rules=serialize($rules);
        return $this->UpdateRouteDb($id,$regexp_value, $value, $module,$action,$rules,$twig, $parent, $level);
    }

    /**
     * @param $id int
     * @param $regexp_value string
     * @return bool|mysqli_result|string
     */
    function UpdateRouteRegexp($id, $regexp_value){
        return $this->UpdateRouteRegexpDb($id,$regexp_value);
    }

    function ProcessUpdateRouteRegexp ($id) {
        $info=$this->GetRouteInfo($id);
        $parent_info=$this->GetRouteInfo($info['parent_id']);
        $regexp=$parent_info['regexp_value'].'/'.$info['value'];
        $data_test=$this->GetRoutesByRegexp($regexp);
        if ( $data_test && $info['id']!=$data_test['id']) {

        }
        else {
            $this->UpdateRouteRegexp($id,$regexp);
        }

    }

    /**
     * @param $id int
     * @param $regexp_value string
     * @return bool|mysqli_result|string
     */
    function UpdateRouteRegexpDb($id, $regexp_value){
        return $this->db->query_write("UPDATE `core_routes`
        SET
        `regexp_value`=".$this->db->sql_prepare($regexp_value)."
        WHERE `id`=".$this->db->sql_prepare($id));
    }
    /**
     * @param $id int
     * @param $regexp_value string
     * @param $value string
     * @param $module string
     * @param $action string
     * @param $rules array
     * @param $twig string
     * @param $parent int
     * @param $level int
     * @return bool|mysqli_result|string
     */
    function UpdateRouteDb($id, $regexp_value, $value, $module, $action, $rules, $twig, $parent, $level){
        return $this->db->query_write("UPDATE `core_routes`
        SET
        `regexp_value`=".$this->db->sql_prepare($regexp_value).",
        `value`=".$this->db->sql_prepare($value).",
        `module`=".$this->db->sql_prepare($module).",
        `action`=".$this->db->sql_prepare($action).",
        `rules`=".$this->db->sql_prepare($rules).",
        `twig`=".$this->db->sql_prepare($twig).",
        `parent_id`=".$this->db->sql_prepare($parent).",
        `level`=".$this->db->sql_prepare($level)."
        WHERE `id`=".$this->db->sql_prepare($id));
    }

    
    function UpdateChildRegexp ($id) {
        $info=$this->GetRouteInfo($id);
        $child_list=$this->GetChildrenRoutes($info['id']);
        foreach ($child_list as $child) {
            $child_regexp_parts=explode('/',$child['regexp_value']);
            $child_regexp_parts[$info['level']]=$info['value'];
            $child_regexp_value=implode('/',$child_regexp_parts);

            ///
            $data_test=$this->GetRoutesByRegexp($child_regexp_value);
            if ( $data_test && $child['id']!=$data_test['id']) {

            }
            else {
                $this->UpdateRouteRegexp($child['id'], $child_regexp_value);
            }
        }
    }

    /*
     OLD CODE
     */









    function PrepareRoutesWhere ($filter_options){
        $sql='';
        if (intval($filter_options['id'])>0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_routes`.`id`=".$this->db->sql_prepare($filter_options['id']);
        }
        if ($filter_options['controller']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_routes`.`controller`=".$this->db->sql_prepare($filter_options['controller']);
        }
        return $sql;
    }

    function GetRoutesTotal ($filter_options=array()) {

        $result=$this->GetRoutesTotalFromDb($filter_options);
        return intval($result['count']);
    }

    function GetRoutesTotalFromDb($filter_options){
        $sql=$this->PrepareRoutesWhere($filter_options);

        return $this->db->query_first("SELECT count(`core_routes`.`id`) as count
        FROM `core_routes`
        ".$sql);
    }



    
}